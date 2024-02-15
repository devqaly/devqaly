<template>
  <div
    class="flex justify-between items-center rounded-lg bg-red-50 text-red-700 border border-red-200 p-3"
    v-if="endUntilTrial && shouldShowSubscriptionWarning()"
  >
    <div class="w-full md:max-w-[600px] flex-wrap">
      <div class="font-semibold">Payment method missing</div>

      <div class="mt-1">
        Your trial is ending in {{ endUntilTrial.value }} {{ endUntilTrial.unit }} and subscription
        have been chosen. Switch to a paid plan to avoid losing access to your sessions, projects
        and allow team members to see sessions.
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
  isWithinRangeForWarningTrialEnding,
  shouldShowSubscriptionConcerns
} from '@/services/resources/Subscription'
import { differenceInDays, intervalToDuration } from 'date-fns'
import differenceInMinutes from 'date-fns/differenceInMinutes'

const portalUrl = ref<null | string>(null)

const isFetchingPortalUrl = ref(true)

const appStore = useAppStore()

const endUntilTrial = computed<{ unit: string; value: number } | null>(() => {
  assertsIsCompanyCodec(appStore.activeCompany)

  if (!appStore.activeCompany.trialEndsAt) return null

  const daysUntilEndTrial = differenceInDays(
    new Date(appStore.activeCompany.trialEndsAt),
    new Date()
  )

  if (daysUntilEndTrial > 0)
    return { unit: daysUntilEndTrial > 1 ? 'days' : 'day', value: daysUntilEndTrial }

  const minutesUntilEndTrial = differenceInMinutes(
    new Date(appStore.activeCompany.trialEndsAt),
    new Date()
  )

  return { unit: minutesUntilEndTrial > 1 ? 'minutes' : 'minute', value: minutesUntilEndTrial }
})

const hasError = ref(false)

onBeforeMount(getCompanyPortalUrl)

function shouldShowSubscriptionWarning(): boolean {
  if (!shouldShowSubscriptionConcerns()) return false

  assertsIsCompanyCodec(appStore.activeCompany)

  if (endUntilTrial.value === null) return false

  return isWithinRangeForWarningTrialEnding(appStore.activeCompany.trialEndsAt!)
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
