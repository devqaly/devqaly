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

    <RouterLink
      :to="{ name: 'companySubscription', params: { companyId: route.params.companyId } }"
    >
      <Button
        class="!text-red-500"
        link
        :loading="isFetchingPortalUrl"
      >
        Manage Subscription
      </Button>
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { ref, toRef } from 'vue'
import { useAppStore } from '@/stores/app'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import { useCompanyTrial } from '@/composables/company/useCompanyTrial'
import { useRoute } from 'vue-router'

const isFetchingPortalUrl = ref(true)

const appStore = useAppStore()

const route = useRoute()

assertsIsCompanyCodec(appStore.activeCompany)

const { shouldShowSubscriptionEndingWarning, endUntilTrial } = useCompanyTrial(
  toRef(appStore.activeCompany)
)
</script>
