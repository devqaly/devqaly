import type { DateTime } from '@/services/api'
import type { SUBSCRIPTION_PLANS } from '@/services/resources/Subscription'

export interface SubscriptionCodec {
  createdAt: DateTime
  endsAt: DateTime | null
  status:
    | 'active'
    | 'past_due'
    | 'unpaid'
    | 'canceled'
    | 'incomplete'
    | 'incomplete_expired'
    | 'trialing'
    | 'paused'
    | 'all'
    | 'ended'
  trialEndsAt: DateTime | null
  planName: SUBSCRIPTION_PLANS
}
