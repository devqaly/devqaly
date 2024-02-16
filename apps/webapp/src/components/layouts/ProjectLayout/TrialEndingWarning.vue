<template>
  <div
    class="flex justify-between items-center rounded-lg bg-red-50 text-red-700 border border-red-200 p-3"
    v-if="shouldShowSubscriptionEndingWarning && endUntilTrial"
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
import { onBeforeMount, ref, toRef } from 'vue'
import { getCompanyStripePortalUrl } from '@/services/api/resources/company/actions'
import { useAppStore } from '@/stores/app'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import {
  isWithinRangeForWarningTrialEnding,
  shouldShowSubscriptionConcerns
} from '@/services/resources/Subscription'
import { useCompanyTrial } from '@/composables/company/useCompanyTrial'

const portalUrl = ref<null | string>(null)

const isFetchingPortalUrl = ref(true)

const hasError = ref(false)

const appStore = useAppStore()

assertsIsCompanyCodec(appStore.activeCompany)

const { shouldShowSubscriptionEndingWarning, endUntilTrial } = useCompanyTrial(
  toRef(appStore.activeCompany)
)

onBeforeMount(getCompanyPortalUrl)

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
