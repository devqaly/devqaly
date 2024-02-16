<template>
  <Dialog
    v-model:visible="visible"
    modal
    :draggable="false"
  >
    <template #header>
      <span class="font-bold"> Choose your plan </span>
    </template>

    <div class="grid grid-cols-3 gap-10">
      <PlanSection
        :plan-selected="currentPlan === 'free'"
        plan-display-name="Free Plan"
        plan-name="free"
        plan-pricing="No Charges"
        plan-description="Perfect for trying out Devqaly"
        :plan-advantages="PLAN_FREE_ADVANTAGES"
      />

      <PlanSection
        :plan-selected="currentPlan === 'gold'"
        plan-display-name="Gold Plan"
        plan-name="gold"
        plan-pricing="10$ per member"
        plan-description="Perfect for small and medium companies"
        :plan-advantages="PLAN_GOLD_ADVANTAGES"
      />

      <PlanSection
        :plan-selected="currentPlan === 'enterprise'"
        plan-display-name="Enterprise Plan"
        plan-name="enterprise"
        plan-pricing="Custom"
        plan-description="Perfect for enterprise clients"
        :plan-advantages="PLAN_ENTERPRISE_ADVANTAGES"
      >
        <template #cta>
          <a href="mailto:bruno.francisco@devqaly.com">
            <Button
              label="Contact Us"
              class="w-full"
            />
          </a>
        </template>
      </PlanSection>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog'
import PlanSection from '@/components/subscription/ChooseSubscriptionDialog/PlanSection.vue'
import {
  PLAN_ENTERPRISE_ADVANTAGES,
  PLAN_FREE_ADVANTAGES,
  PLAN_GOLD_ADVANTAGES,
  SUBSCRIPTION_PLANS
} from '@/services/resources/Subscription'

const visible = defineModel<boolean>('visible', { required: true })

defineProps<{
  currentPlan: SUBSCRIPTION_PLANS
}>()
</script>
