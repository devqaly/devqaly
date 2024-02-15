<template>
  <div
    class="flex justify-between items-center rounded-lg bg-red-50 text-red-700 border border-red-200 p-3"
    v-show="shouldShowSubscriptionWarning()"
  >
    <div class="w-full md:max-w-[600px] flex-wrap">
      <div class="font-semibold">Payment method missing</div>

      <div class="mt-1">
        Your trial is ending in {{ daysUntilEndTrial }} days and no payment method have been added.
        Please, add a payment method to avoid losing access to your sessions and allow team members
        to see sessions.
      </div>
    </div>

    <a :href="portalUrl">
      <Button
        class="!text-red-500"
        link
        :loading="isFetchingPortalUrl"
        >Add Payment Method</Button
      >
    </a>
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, ref } from 'vue'
import { getCompanyStripePortalUrl } from '@/services/api/resources/company/actions'
import { useAppStore } from '@/stores/app'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import {
  hasPaymentMethod,
  isActiveSubscription,
  isWithinRangeForWarningTrialEnding,
  shouldShowSubscriptionConcerns
} from '@/services/resources/Subscription'
import differenceInDays from 'date-fns/differenceInDays'

const portalUrl = ref<null | string>(null)

const isFetchingPortalUrl = ref(true)

const appStore = useAppStore()

const daysUntilEndTrial = computed<number>(() => {
  assertsIsCompanyCodec(appStore.activeCompany)

  if (!appStore.activeCompany.trialEndsAt) return -1

  return differenceInDays(new Date(appStore.activeCompany.trialEndsAt), new Date())
})

const hasError = ref(false)

onBeforeMount(getCompanyPortalUrl)

function shouldShowSubscriptionWarning(): boolean {
  if (!shouldShowSubscriptionConcerns()) return false

  assertsIsCompanyCodec(appStore.activeCompany)

  return (
    isWithinRangeForWarningTrialEnding(daysUntilEndTrial.value) &&
    !hasPaymentMethod(appStore.activeCompany)
  )
}

async function getCompanyPortalUrl() {
  assertsIsCompanyCodec(appStore.activeCompany)

  try {
    const { data } = await getCompanyStripePortalUrl(appStore.activeCompany.id)
    portalUrl.value = data.data.portalUrl
  } catch (e) {
    hasError.value = true
  } finally {
    isFetchingPortalUrl.value = false
  }
}
</script>
