<template>
  <Dialog
    v-model:visible="visible"
    modal
    :draggable="false"
    :pt="{
      content: { class: planSelected ? 'rounded-lg !p-5' : undefined },
      header: { class: planSelected ? '!hidden' : undefined }
    }"
    :style="{
      'max-width': planSelected ? '600px' : '900px'
    }"
  >
    <template #header>
      <span class="font-bold text-xl"> Choose your plan </span>
    </template>

    <div
      :class="{
        'grid gap-10 transition-all w-full': true,
        'grid-cols-3': !planSelected,
        'grid-cols-1': planSelected
      }"
    >
      <PlanSection
        v-if="!planSelected"
        :plan-selected="currentPlan === 'free'"
        plan-display-name="Free Plan"
        plan-name="free"
        plan-pricing="No Charges"
        plan-description="Perfect for trying out Devqaly"
        :plan-advantages="PLAN_FREE_ADVANTAGES"
        :loading="loading"
      >
        <template #cta>
          <Button
            :disabled="currentPlan === 'free'"
            :label="currentPlan === 'free' ? 'Current' : 'Select Plan'"
            class="w-full"
            :loading="loading"
            @click="onChosePlan('free')"
          />
        </template>
      </PlanSection>

      <PlanSection
        v-if="!planSelected"
        :plan-selected="currentPlan === 'gold'"
        plan-display-name="Gold Plan"
        plan-name="gold"
        plan-pricing="10$ per member"
        plan-description="Perfect for small and medium companies"
        :plan-advantages="PLAN_GOLD_ADVANTAGES"
        :loading="loading"
      >
        <template #cta>
          <Button
            :disabled="currentPlan === 'gold'"
            :label="currentPlan === 'gold' ? 'Current' : 'Select Plan'"
            class="w-full"
            :loading="loading"
            @click="onChosePlan('gold')"
          />
        </template>
      </PlanSection>

      <PlanSection
        v-if="!planSelected"
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
              :loading="loading"
            />
          </a>
        </template>
      </PlanSection>

      <ChoosePlanWarning
        :current-plan="currentPlan"
        :selected-plan="planSelected"
        :loading="loading"
        v-if="planSelected"
        @close="planSelected = null"
        @accepted="onAcceptedChangePlan"
      />
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog'
import PlanSection from '@/components/subscription/ChooseSubscriptionDialog/PlanSection.vue'
import {
  PLAN_ENTERPRISE_ADVANTAGES,
  PLAN_FREE_ADVANTAGES,
  PLAN_GOLD_ADVANTAGES
} from '@/services/resources/Subscription'
import type { POSSIBLE_CHANGE_PLANS, SUBSCRIPTION_PLANS } from '@/services/resources/Subscription'
import { ref } from 'vue'
import ChoosePlanWarning from '@/components/subscription/ChooseSubscriptionDialog/ChoosePlanWarning.vue'

const visible = defineModel<boolean>('visible', { required: true })

const planSelected = ref<POSSIBLE_CHANGE_PLANS | null>(null)

defineProps<{
  currentPlan: SUBSCRIPTION_PLANS
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'chose:plan', plan: POSSIBLE_CHANGE_PLANS): void
}>()

function onChosePlan(plan: POSSIBLE_CHANGE_PLANS) {
  planSelected.value = plan
}

function onAcceptedChangePlan() {
  if (planSelected.value === null) {
    throw new Error('`planSelected` should be defined')
  }

  emit('chose:plan', planSelected.value as POSSIBLE_CHANGE_PLANS)
  planSelected.value = null
}
</script>
