<template>
  <div class="p-5">
    <div class="text-3xl font-medium">Subscription</div>
    <div class="font-medium text-slate-500 mb-4">Manage your company's subscription here</div>

    <TrialWarningSection />
    <YourPlanDetailsSection class="mt-4" />

    <div class="grid grid-cols-1 md:grid-cols-2 mt-8 gap-10">
      <PaymentDetailsSection />
      <BillingHistorySection />
    </div>
  </div>
</template>
<script setup lang="ts">
import TrialWarningSection from '@/components/pages/companies/SubscriptionView/TrialWarningSection.vue'
import BillingHistorySection from '@/components/pages/companies/SubscriptionView/BillingHistorySection.vue'
import PaymentDetailsSection from '@/components/pages/companies/SubscriptionView/PaymentDetailsSection/PaymentDetailsSection.vue'
import YourPlanDetailsSection from '@/components/pages/companies/SubscriptionView/YourPlanDetailsSection.vue'
import { onBeforeMount } from 'vue'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import { useAppStore } from '@/stores/app'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'

const appStore = useAppStore()

onBeforeMount(getCompanyPortalUrl)

async function getCompanyPortalUrl() {
  assertsIsCompanyCodec(appStore.activeCompany)

  try {
    await appStore.getActiveCompanyPortalStripePortalUrl(window.location.href)
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  }
}
</script>
