import { describe, expect, it } from 'vitest'
import addDays from 'date-fns/addDays'
import { companyFactoryCodec, subscriptionFactoryCodec } from '@/services/factories/companyFactory'
import { useCompanyTrial } from '@/composables/company/useCompanyTrial'
import { toRef } from 'vue'
import subDays from 'date-fns/subDays'

describe('useCompanyTrial', () => {
  it('should return correct values when trial is in the future', () => {
    const futureDate = addDays(new Date(), 2)

    const company: ReturnType<typeof companyFactoryCodec> = {
      ...companyFactoryCodec(),
      trialEndsAt: futureDate.toUTCString()
    }

    const {
      trialEndDateFormatted,
      endUntilTrial,
      companyHasActiveSubscription,
      isCompanyTrialing,
      shouldShowSubscriptionEndingWarning,
      shouldShowCompanyIsTrialingWarning
    } = useCompanyTrial(toRef(company))

    expect(trialEndDateFormatted.value).to.be.eq(futureDate.toLocaleDateString())
    expect(endUntilTrial.value).to.not.be.null
    expect(endUntilTrial.value?.unit).to.be.eq('day')
    expect(endUntilTrial.value?.value).to.be.eq(1)
    expect(companyHasActiveSubscription.value).to.be.eq(false)
    expect(isCompanyTrialing.value).to.be.eq(true)
    expect(shouldShowSubscriptionEndingWarning.value).to.be.eq(true)
    expect(shouldShowCompanyIsTrialingWarning.value).to.be.eq(false)
  })

  it('should not show that company trialing and trialing is ending at the same time', () => {
    const futureDate = addDays(new Date(), 2)

    const companyWithTrialEnding: ReturnType<typeof companyFactoryCodec> = {
      ...companyFactoryCodec(),
      trialEndsAt: futureDate.toUTCString()
    }

    const { shouldShowSubscriptionEndingWarning, shouldShowCompanyIsTrialingWarning } =
      useCompanyTrial(toRef(companyWithTrialEnding))

    expect(shouldShowCompanyIsTrialingWarning.value).to.be.eq(false)
    expect(shouldShowSubscriptionEndingWarning.value).to.be.eq(true)

    const companyWithTrial: ReturnType<typeof companyFactoryCodec> = {
      ...companyFactoryCodec(),
      trialEndsAt: addDays(new Date(), 15).toUTCString()
    }

    const {
      shouldShowSubscriptionEndingWarning: _shouldShowSubscriptionEndingWarning,
      shouldShowCompanyIsTrialingWarning: _shouldShowCompanyIsTrialingWarning
    } = useCompanyTrial(toRef(companyWithTrial))

    expect(_shouldShowCompanyIsTrialingWarning.value).to.be.eq(true)
    expect(_shouldShowSubscriptionEndingWarning.value).to.be.eq(false)
  })

  it('should return correct values when trial is in the future', () => {
    const company: ReturnType<typeof companyFactoryCodec> = {
      ...companyFactoryCodec(),
      trialEndsAt: subDays(new Date(), 2).toUTCString()
    }

    const {
      trialEndDateFormatted,
      endUntilTrial,
      companyHasActiveSubscription,
      isCompanyTrialing,
      shouldShowSubscriptionEndingWarning,
      shouldShowCompanyIsTrialingWarning
    } = useCompanyTrial(toRef(company))

    expect(trialEndDateFormatted.value).to.be.eq('')
    expect(endUntilTrial.value).to.be.null
    expect(companyHasActiveSubscription.value).to.be.eq(false)
    expect(isCompanyTrialing.value).to.be.eq(false)
    expect(shouldShowSubscriptionEndingWarning.value).to.be.eq(false)
    expect(shouldShowCompanyIsTrialingWarning.value).to.be.eq(false)
  })

  it('should not show trial warning if user has active subscription', () => {
    const company: ReturnType<typeof companyFactoryCodec> = {
      ...companyFactoryCodec(),
      trialEndsAt: addDays(new Date(), 2).toUTCString(),
      subscription: subscriptionFactoryCodec()
    }

    const { shouldShowSubscriptionEndingWarning } = useCompanyTrial(toRef(company))

    expect(shouldShowSubscriptionEndingWarning.value).to.be.eq(false)
  })
})
