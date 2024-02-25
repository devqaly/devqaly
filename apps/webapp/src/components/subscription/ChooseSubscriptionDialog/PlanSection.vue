<template>
  <div class="flex flex-col">
    <div class="shrink">
      <div class="font-semibold text-xl flex gap-2 items-center">
        {{ planDisplayName }}
        <Tag
          v-if="planName === 'gold'"
          class="!bg-green-50 !border !border-green-500 !text-green-500 !text-[10px]"
          rounded
          severity="success"
          value="Most Popular"
        />
      </div>
      <div class="text-gray-500">{{ planPricing }}</div>
    </div>

    <ol class="flex flex-col gap-2 grow mt-2">
      <li
        class="flex gap-2 mt-2"
        v-for="advantage in planAdvantages"
        :key="`${planName}-${advantage.name}`"
      >
        <div
          :class="{
            ' px-2 py-0.5 rounded-full': true,
            'bg-green-50': advantage.supported,
            'bg-red-50': !advantage.supported
          }"
        >
          <i
            class="pi pi-check text-green-500"
            v-if="advantage.supported"
          ></i>
          <i
            class="pi pi-times text-red-500"
            v-else
          ></i>
        </div>
        {{ advantage.name }}
      </li>
    </ol>

    <div class="mt-5">
      <slot name="cta">
        <Button
          :label="planSelected ? 'Current Plan' : 'Choose Plan'"
          class="w-full"
          :disabled="planSelected"
          :loading="loading"
          @click="onChosePlan"
        />
      </slot>
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { SUBSCRIPTION_PLANS } from '@/services/resources/Subscription'
import type { POSSIBLE_CHANGE_PLANS } from '@/services/resources/Subscription'

const props = defineProps<{
  planSelected: boolean
  planName: SUBSCRIPTION_PLANS
  planDisplayName: string
  planPricing: string
  planAdvantages: { name: string; supported: boolean }[]
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'chose:plan', plan: POSSIBLE_CHANGE_PLANS): void
}>()

function onChosePlan() {
  if (props.planName === 'enterprise') {
    throw new Error('Enterprise should not be able to be chosen by the user')
  }

  emit('chose:plan', props.planName)
}
</script>
