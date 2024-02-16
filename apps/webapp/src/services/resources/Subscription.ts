import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import differenceInMinutes from 'date-fns/differenceInMinutes'
import isFuture from 'date-fns/isFuture'

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

export function isTrialing(trialEndsAt: CompanyCodec['trialEndsAt']): boolean {
  return trialEndsAt !== null && isFuture(new Date(trialEndsAt!))
}

export function hasActiveSubscription(company: CompanyCodec): boolean {
  if (company.subscription === null || company.subscription === undefined) return false

  return company.subscription.status === 'active' || company.subscription.status === 'trialing'
}

export function hasPaymentMethod(
  method: Pick<CompanyCodec, 'paymentLastFourDigits' | 'paymentMethodType'>
): boolean {
  return !!(method.paymentMethodType && method.paymentLastFourDigits)
}

export function isWithinRangeForWarningTrial(date: string): boolean {
  return isFuture(new Date(date))
}

export function isWithinRangeForWarningTrialEnding(date: string): boolean {
  const minutesUntilTrialEnds = differenceInMinutes(new Date(date), new Date())

  // 10 days = 14400 minutes
  return minutesUntilTrialEnds >= 0 && minutesUntilTrialEnds < 14400
}
