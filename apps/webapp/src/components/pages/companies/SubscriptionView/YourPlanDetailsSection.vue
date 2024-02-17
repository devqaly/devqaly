<template>
  <section>
    <ChooseSubscriptionDialog
      v-model:visible="isChoosingPlan"
      :current-plan="selectedSubscription"
      :loading="isChangingPlan"
      @chose:plan="onPlanChange"
    />

    <h2 class="font-semibold text-lg">Your plan details</h2>
    <div class="bg-white shadow p-10 rounded-lg mt-1">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-[2.8rem] font-bold leading-[normal] capitalize">
            {{
              companyHasActiveSubscription
                ? `${appStore.activeCompany!.subscription!.planName} (Monthly)`
                : 'Free Plan'
            }}
          </h3>
          <div
            class="text-gray-500 text-xl mt-2"
            v-text="subtext"
          />
        </div>

        <Button
          label="Update Plan"
          icon-pos="right"
          icon="pi pi-chevron-right"
          @click="onChoosePlanClick"
        />
      </div>

      <Divider class="!my-10" />

      <div class="flex justify-between">
        <div class="font-semibold text-xl">Total Price Per Month:</div>
        <div class="font-semibold text-xl hover:cursor-pointer">
          <a
            :href="appStore.activeCompanyStripePortalUrl"
            class="flex gap-4 items-center"
          >
            Open Customer Portal <i class="pi pi-external-link"></i>
          </a>
        </div>
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
  POSSIBLE_CHANGE_PLANS,
  SUBSCRIPTION_PLANS
} from '@/services/resources/Subscription'
import ChooseSubscriptionDialog from '@/components/subscription/ChooseSubscriptionDialog/ChooseSubscriptionDialog.vue'
import { useToast } from 'primevue/usetoast'
import { displayGeneralError } from '@/services/ui'

const isChoosingPlan = ref(false)

const isChangingPlan = ref(false)

const toast = useToast()

const appStore = useAppStore()

const companyHasActiveSubscription = computed(() => hasActiveSubscription(appStore.activeCompany!))

const selectedSubscription = computed<SUBSCRIPTION_PLANS>(() => {
  if (!hasActiveSubscription(appStore.activeCompany!)) return 'free'

  return appStore.activeCompany!.subscription!.planName
})

const subtext = computed(() => {
  if (!hasActiveSubscription(appStore.activeCompany!)) return 'Ideal to people trying out Devqaly'

  if (appStore.activeCompany!.subscription!.planName === 'gold') {
    if (appStore.activeCompany!.subscription!.endsAt) {
      return `Subscription ends at ${new Date(
        appStore.activeCompany!.subscription!.endsAt
      ).toLocaleDateString()}`
    }

    return 'Perfect for small and medium companies'
  }

  return 'Perfect for enterprise companies with 100+ engineering team members'
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

async function onPlanChange(plan: POSSIBLE_CHANGE_PLANS) {
  try {
    isChangingPlan.value = true
    await appStore.updateActiveCompanySubscription({ newPlan: plan })
    isChoosingPlan.value = false
  } catch (e) {
    displayGeneralError(e)
  } finally {
    isChangingPlan.value = false
  }
}
</script>
