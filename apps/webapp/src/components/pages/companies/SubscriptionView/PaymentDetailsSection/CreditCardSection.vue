<template>
  <div class="flex justify-between">
    <h2 class="font-semibold text-xl">Credit Card</h2>
    <a
      data-cy="company-subscription-view__stripe-portal-url"
      :href="appStore.activeCompanyStripePortalUrl"
      class="text-green-500 hover:underline px-2 py-1"
    >
      Edit
    </a>
  </div>
  <div
    data-cy="company-subscription-view__no-payment-method-added-warning"
    class="flex gap-5 justify-between bg-red-200 border-red-300 text-red-500 px-5 py-2 rounded mt-2"
    v-if="!companyHasPaymentMethod"
  >
    <div>Company does not have a payment method added</div>

    <a
      :href="appStore.activeCompanyStripePortalUrl"
      class="text-red-500 hover:underline"
    >
      Add one
    </a>
  </div>

  <div
    class="mt-2"
    v-if="companyHasPaymentMethod"
  >
    <span class="px-4 py-2 border border-gray-200 rounded">
      {{ appStore.activeCompany!.paymentMethodType }}
    </span>
    •••• •••• •••• {{ appStore.activeCompany!.paymentLastFourDigits }}
  </div>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { computed } from 'vue'
import { hasPaymentMethod } from '@/services/resources/Subscription'

const appStore = useAppStore()

const companyHasPaymentMethod = computed(() => hasPaymentMethod(appStore.activeCompany))
</script>
