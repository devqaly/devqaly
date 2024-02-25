<template>
  <Dialog
    v-model:visible="isDialogOpen"
    modal
    :show-header="false"
    :draggable="false"
    :style="{ maxWidth: '580px' }"
    :pt="{ content: { class: 'rounded-lg !p-5' } }"
  >
    <h2 class="text-xl font-semibold">Your company is blocked</h2>
    This happens either because payment have not been made or your trial have ended.

    <div class="mt-4 flex flex-col gap-2">
      <component
        :is="components[reason.reason]"
        :key="reason.reason"
        :reason="reason"
        :company="company"
        v-for="reason in company.blockedReasons!"
      />
    </div>
  </Dialog>
</template>

<script lang="ts" setup>
import { defineComponent, onBeforeMount, ref } from 'vue'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import { hasBlockedReasons } from '@/services/resources/Company'
import { BlockedReasons } from '@/services/api/resources/company/codec'
import TrialFinishedAndHasMoreProjectsThanAllowedOnFreePlan from '@/components/subscription/CompanyBlockedDialog/types/TrialFinishedAndHasMoreProjectsThanAllowedOnFreePlan.vue'
import SubscriptionWasNotPaid from '@/components/subscription/CompanyBlockedDialog/types/SubscriptionWasNotPaid.vue'
import TrialFinishedAndHasMoreMembersThanAllowedOnFreePlan from '@/components/subscription/CompanyBlockedDialog/types/TrialFinishedAndHasMoreMembersThanAllowedOnFreePlan.vue'

const components: Record<BlockedReasons, ReturnType<typeof defineComponent>> = {
  [BlockedReasons.TRIAL_FINISHED_AND_HAS_MORE_PROJECTS_THAN_ALLOWED_ON_FREE_PLAN]:
    TrialFinishedAndHasMoreProjectsThanAllowedOnFreePlan,
  [BlockedReasons.TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN]:
    TrialFinishedAndHasMoreMembersThanAllowedOnFreePlan,
  [BlockedReasons.SUBSCRIPTION_WAS_NOT_PAID]: SubscriptionWasNotPaid
}

const isDialogOpen = ref(false)

const props = defineProps<{
  company: CompanyCodec
}>()

onBeforeMount(() => {
  if (hasBlockedReasons(props.company.blockedReasons)) {
    isDialogOpen.value = true
  }
})
</script>
