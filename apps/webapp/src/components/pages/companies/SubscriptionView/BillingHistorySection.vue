<template>
  <section class="flex flex-col">
    <h2 class="font-semibold text-lg shrink">Billing History</h2>
    <div class="bg-white shadow p-2 rounded-lg mt-1 grow justify-center items-center flex flex-col">
      <i class="pi pi-file !text-[90px]" />

      <a :href="appStore.activeCompanyStripePortalUrl as string">
        <Button
          class="!mt-4 !text-gray-500 !text-center"
          label="See on customer portal"
          text
          icon-pos="right"
          icon="pi pi-external-link"
        />
      </a>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
</script>
