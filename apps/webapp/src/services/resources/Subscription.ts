import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'
import type { CompanyCodec } from '@/services/api/resources/company/codec'

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

export function hasPaymentMethod(
  method: Pick<CompanyCodec, 'paymentLastFourDigits' | 'paymentMethodType'>
): boolean {
  return !!(method.paymentMethodType && method.paymentLastFourDigits)
}

export function isWithinRangeForWarningTrialEnding(days: number): boolean {
  return days > 0 && days < 11
}
