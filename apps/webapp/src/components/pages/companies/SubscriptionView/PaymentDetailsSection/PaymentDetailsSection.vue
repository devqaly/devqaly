<template>
  <section>
    <h2 class="font-semibold text-lg">Payment Details</h2>
    <div class="bg-white shadow p-10 rounded-lg mt-1">
      <CreditCardSection />
      <InvoiceDetailsSection />
      <BillingContactSection />
    </div>
  </section>
</template>

<script setup lang="ts">
import CreditCardSection from './CreditCardSection.vue'
import InvoiceDetailsSection from './InvoiceDetailsSection.vue'
import BillingContactSection from './BillingContactSection.vue'
</script>
