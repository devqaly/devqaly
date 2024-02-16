import type { Ref } from 'vue'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import { computed } from 'vue'
import {
  hasActiveSubscription,
  isTrialing,
  isWithinRangeForWarningTrial,
  isWithinRangeForWarningTrialEnding
} from '@/services/resources/Subscription'
import { differenceInDays } from 'date-fns'
import differenceInMinutes from 'date-fns/differenceInMinutes'

export function useCompanyTrial(company: Ref<CompanyCodec>) {
  const trialEndDateFormatted = computed(() => {
    if (!isTrialing(company.value.trialEndsAt)) return ''

    return new Date(company.value.trialEndsAt!).toLocaleDateString()
  })

  const shouldShowCompanyIsTrialingWarning = computed(() => {
    if (!isTrialing(company.value.trialEndsAt)) return false

    if (shouldShowSubscriptionEndingWarning.value) return false

    return isWithinRangeForWarningTrial(company.value.trialEndsAt!)
  })

  const shouldShowSubscriptionEndingWarning = computed(() => {
    if (!isTrialing(company.value.trialEndsAt)) return false

    return (
      isWithinRangeForWarningTrialEnding(company.value.trialEndsAt!) &&
      !companyHasActiveSubscription.value
    )
  })

  const companyHasActiveSubscription = computed(() => {
    return hasActiveSubscription(company.value)
  })

  const endUntilTrial = computed<{ unit: string; value: number } | null>(() => {
    if (!isTrialing(company.value.trialEndsAt)) return null

    const daysUntilEndTrial = differenceInDays(new Date(company.value.trialEndsAt!), new Date())

    if (daysUntilEndTrial > 0)
      return { unit: daysUntilEndTrial > 1 ? 'days' : 'day', value: daysUntilEndTrial }

    const minutesUntilEndTrial = differenceInMinutes(
      new Date(company.value.trialEndsAt!),
      new Date()
    )

    return { unit: minutesUntilEndTrial > 1 ? 'minutes' : 'minute', value: minutesUntilEndTrial }
  })

  const isCompanyTrialing = computed(() => isTrialing(company.value.trialEndsAt))

  return {
    trialEndDateFormatted,
    endUntilTrial,
    companyHasActiveSubscription,
    isCompanyTrialing,
    shouldShowSubscriptionEndingWarning,
    shouldShowCompanyIsTrialingWarning
  }
}
