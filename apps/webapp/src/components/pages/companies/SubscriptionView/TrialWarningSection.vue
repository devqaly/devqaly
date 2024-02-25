<template>
  <div class="flex flex-col gap-2">
    <section
      class="flex justify-between items-center rounded-lg bg-yellow-50 text-yellow-700 border border-red-200 p-3"
      v-if="shouldShowCompanyIsTrialingWarning"
    >
      <div class="w-full flex-wrap">
        <div class="font-semibold">Currently in trial mode</div>

        <div class="mt-1">
          This company is currently trialing and the trial ends at {{ trialEndDateFormatted }}
        </div>
      </div>
    </section>

    <section
      class="flex justify-between items-center rounded-lg bg-red-50 text-red-700 border border-red-200 p-3"
      v-if="shouldShowSubscriptionEndingWarning"
    >
      <div class="w-full flex-wrap">
        <div class="font-semibold">Subscription not added</div>

        <div class="mt-1">
          Your trial is ending in {{ endUntilTrial!.value }} {{ endUntilTrial!.unit }} and no
          subscription have been chosen. Switch to a paid plan to avoid losing access to your
          sessions, projects and allow team members to see sessions.
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { toRef } from 'vue'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import { useCompanyTrial } from '@/composables/company/useCompanyTrial'

const appStore = useAppStore()

assertsIsCompanyCodec(appStore.activeCompany)

const {
  trialEndDateFormatted,
  endUntilTrial,
  shouldShowSubscriptionEndingWarning,
  shouldShowCompanyIsTrialingWarning
} = useCompanyTrial(toRef(appStore.activeCompany))
</script>
