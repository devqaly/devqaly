<template>
  <section>
    <ChooseSubscriptionDialog
      v-model:visible="isChoosingPlan"
      :current-plan="selectedSubscription"
    />

    <h2 class="font-semibold text-lg">Your plan details</h2>
    <div class="bg-white shadow p-10 rounded-lg mt-1">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-[2.8rem] font-bold leading-[normal]">
            {{ companyHasActiveSubscription ? 'HAS ACTIVE SUBSCRIPTION' : 'Free Plan' }}
          </h3>
          <div
            class="text-gray-500 text-xl mt-2"
            v-text="subtext"
          />
        </div>

        <Button
          label="Update Plan"
          icon-pos="right"
          severity="success"
          icon="pi pi-chevron-right"
          @click="onChoosePlanClick"
        />
      </div>

      <Divider class="!my-10" />

      <div class="flex justify-between">
        <div class="font-semibold text-xl">Total Price Per Month:</div>
        <div class="font-semibold text-xl">{{ totalPricePerMonth }}$</div>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { computed, ref } from 'vue'
import {
  hasActiveSubscription,
  hasPaymentMethod,
  SUBSCRIPTION_PLANS
} from '@/services/resources/Subscription'
import ChooseSubscriptionDialog from '@/components/subscription/ChooseSubscriptionDialog/ChooseSubscriptionDialog.vue'
import { useToast } from 'primevue/usetoast'

const isChoosingPlan = ref(false)

const toast = useToast()

const appStore = useAppStore()

const companyHasActiveSubscription = computed(() => hasActiveSubscription(appStore.activeCompany!))

const selectedSubscription = computed<SUBSCRIPTION_PLANS>(() => {
  if (!hasActiveSubscription(appStore.activeCompany!)) return 'free'

  return 'enterprise'
})

const subtext = computed(() => {
  if (!hasActiveSubscription(appStore.activeCompany!)) return 'Ideal to people trying out Devqaly'

  return 'SUBTEXTTTT'
})

const totalPricePerMonth = computed(() => {
  if (!hasActiveSubscription(appStore.activeCompany!)) return 0

  return -1
})

function onChoosePlanClick() {
  if (!hasPaymentMethod(appStore.activeCompany)) {
    toast.add({
      severity: 'error',
      summary: 'No Payment Method',
      detail: 'Please, add a payment method before switching plans',
      life: 3000,
      group: 'bottom-center'
    })

    return
  }

  isChoosingPlan.value = true
}
</script>
