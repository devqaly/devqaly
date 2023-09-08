import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'

export function isActiveSubscription(status: SubscriptionCodec['status']) {
  return status === 'active'
}

/**
 * We only will use subscription in the cloud version of Devqaly.
 * Self-hosted clients won't have to worry about subscriptions.
 */
export function shouldShowSubscriptionConcerns(): boolean {
  return import.meta.env.VITE_DEVQALY_IS_SELF_HOSTING === 'false'
}
