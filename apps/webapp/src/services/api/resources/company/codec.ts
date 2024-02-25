import type { DateTime, ResourceID } from '@/services/api'
import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'

export enum BlockedReasons {
  TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN = 'trialFinishedAndHasMoreMembersThanAllowedOnFreePlan',
  TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN = 'trialFinishedAndHasMoreProjectsThanAllowedOnFreePlan',
  SUBSCRIPTION_WAS_NOT_PAID = 'subscriptionWasNotPaid'
}

export interface BlockedReason {
  reason: BlockedReasons
  description: string
  possibleResolution: string
  subscriptionStatus?: SubscriptionCodec['status']
}

export interface CompanyCodec {
  id: ResourceID
  name: string
  trialEndsAt?: DateTime | null
  paymentMethodType?: string | null
  paymentLastFourDigits?: string | null
  invoiceDetails?: string | null
  billingContact?: string | null
  blockedReasons?: BlockedReason[] | null
  subscription?: SubscriptionCodec | null
}

export interface CompanyStripePortalResponse {
  portalUrl: string
}
