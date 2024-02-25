<?php

namespace App\services\Resources;

use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use App\services\SubscriptionService;
use App\Traits\UsesPaginate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SessionService
{
    use UsesPaginate;

    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function createSession(Collection $data, ?User $user, Project $project): Session
    {
        $session = Session::create([
            'os' => $data->get('os'),
            'platform_name' => $data->get('platformName'),
            'version' => $data->get('version'),
            'window_width' => $data->get('windowWidth'),
            'window_height' => $data->get('windowHeight'),
            'project_id' => $project->id,
            'created_by_id' => $user?->id,
            'environment' => $data->get('environment'),
        ]);

        if ($this->shouldArchivePastSessions($project)) {
            $this->archivePastSessions($project);
        }

        return $session;
    }

    public function listSessions(Collection $filters, User $user, Project $project): LengthAwarePaginator
    {
        $sessions = Session::with('createdBy')->where('project_id', $project->id);

        if ($filters->has('createdAtOrder')) {
            $sessions->orderBy('created_at', $filters->get('createdAtOrder'));
        }

        if ($filters->has('createdByName')) {
            $sessions->whereHas('createdBy', function (Builder $builder) use ($filters) {
                $builder->where(
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name)"),
                    'ILIKE',
                    $filters->get('createdByName') . '%'
                );
            });
        }

        if ($filters->has('os')) {
            $sessions->where('os', 'LIKE', $filters->get('os') . '%');
        }

        if ($filters->has('platform')) {
            $sessions->where('platform_name', 'LIKE', $filters->get('platform') . '%');
        }

        if ($filters->has('version')) {
            $sessions->where('version', 'LIKE', $filters->get('version') . '%');
        }

        return $sessions->paginate($this->getPerPage());
    }

    private function shouldArchivePastSessions(Project $project): bool
    {
        if (config('devqaly.isSelfHosting')) {
            return false;
        }

        if ($this->subscriptionService->isSubscribedToEnterprisePlan($project->company)) {
            return false;
        }

        $currentNumberSessions = Session::query()->where('project_id', $project->id)->count();

        if ($this->subscriptionService->isSubscribedToGoldPlan($project->company)) {
            return $currentNumberSessions >= SubscriptionService::MAXIMUM_NUMBER_SESSIONS_GOLD_PLAN_PER_COMPANY;
        }

        return $currentNumberSessions >= SubscriptionService::MAXIMUM_NUMBER_SESSIONS_FREE_PLAN_PER_COMPANY;

    }

    private function archivePastSessions(Project $project): void
    {
        if ($this->subscriptionService->isSubscribedToEnterprisePlan($project->company)) {
            return;
        }

        if ($this->subscriptionService->isSubscribedToGoldPlan($project->company)) {
            $numberAllowedSessionsForPlan = SubscriptionService::MAXIMUM_NUMBER_SESSIONS_GOLD_PLAN_PER_COMPANY;
        } else {
            $numberAllowedSessionsForPlan = SubscriptionService::MAXIMUM_NUMBER_SESSIONS_FREE_PLAN_PER_COMPANY;
        }

        $sessionsToDelete = Session::query()
            ->select('id')
            ->where('project_id', $project->id)
            ->orderBy('created_at', 'DESC')
            ->skip($numberAllowedSessionsForPlan)
            ->get();

        Session::query()
            ->whereIn('id', $sessionsToDelete->pluck('id'))
            ->delete();
    }
}
