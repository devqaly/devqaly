<?php

namespace App\Enum\Company;

enum CompanyBlockedReasonEnum: string
{
    case TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN = 'trialFinishedAndHasMoreMembersThanAllowedOnFreePlan';
    case TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN = 'trialFinishedAndHasMoreProjectsThanAllowedOnFreePlan';
    case SUBSCRIPTION_WAS_NOT_PAID = 'subscriptionWasNotPaid';
}
