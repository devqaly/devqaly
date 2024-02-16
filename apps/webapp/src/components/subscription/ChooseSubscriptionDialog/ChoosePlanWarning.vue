<template>
  <section>
    <template v-if="currentPlan === 'free'">
      <div class="text-[3.4rem] font-bold text-center text-black">Gold Plan</div>
      <div class="text-gray-400 text-[1.5rem] mt-1 text-center px-5">
        Your subscription will start right away and you will enjoy the features as soon as you start
      </div>
    </template>
    <template v-if="currentPlan === 'gold'">
      <div class="text-[3.4rem] font-bold text-center text-black">You are downgrading</div>
      <div class="text-gray-400 text-[1.5rem] mt-1 text-center px-5">
        You will be billed for this subscription cycle and at the end of your subscription, you will
        be transferred to the free plan
      </div>
    </template>

    <Button
      class="!mt-4 w-full"
      label="Continue"
      @click="onAccept"
    />

    <Button
      class="!mt-2 w-full !text-gray-500"
      label="Cancel"
      text
      @click="onClose"
    />
  </section>
</template>

<script setup lang="ts">
import { POSSIBLE_CHANGE_PLANS, SUBSCRIPTION_PLANS } from '@/services/resources/Subscription'

defineProps<{
  currentPlan: SUBSCRIPTION_PLANS
  selectedPlan: POSSIBLE_CHANGE_PLANS
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'accepted'): void
}>()

function onClose() {
  emit('close')
}

function onAccept() {
  emit('accepted')
}
</script>
