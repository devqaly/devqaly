<?php

namespace App\Http\Requests\Resources\Session\Event;

use App\Models\Session\Event\EventCustomEvent;
use App\Models\Session\Event\EventDatabaseTransaction;
use App\Models\Session\Event\EventElementClick;
use App\Models\Session\Event\EventElementScroll;
use App\Models\Session\Event\EventLog;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Event\EventResizeScreen;
use App\Models\Session\Event\EventUrlChanged;
use App\Models\Session\Session;
use App\services\Resources\Event\Types\BaseEventTypeService;
use App\services\Resources\Event\Types\EventTypeFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CreateSessionEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Session $projectSession */
        $projectSession = $this->projectSession;

        /** @var null|string $secretToken */
        $secretToken = $this->header('x-devqaly-session-secret-token');

        if ($secretToken === null) {
            return false;
        }

        return $secretToken === $projectSession->secret_token;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     * @throws BindingResolutionException
     */
    public function rules(): array
    {
        $this->validate(['type' => [
            'required',
            Rule::in([
                EventNetworkRequest::class,
                EventUrlChanged::class,
                EventElementClick::class,
                EventElementScroll::class,
                EventResizeScreen::class,
                EventDatabaseTransaction::class,
                EventCustomEvent::class,
                EventLog::class,
            ]),
        ]], $this->request->all());

        /** @var EventTypeFactory $factory */
        $factory = app()->make(EventTypeFactory::class);

        $service = $factory->getEventTypeService($this->request->get('type'));

        if (
            BaseEventTypeService::needsSecretTokenValidation($this->request->get('type'))
            && $this->projectSession->project->security_token !== $this->request->get('securityToken')
        ) {
            abort(
                Response::HTTP_FORBIDDEN,
                "Security token passed is invalid. Please, check your project's settings to retrieve the correct one"
            );
        }

        return $service->createValidationRules();
    }
}
