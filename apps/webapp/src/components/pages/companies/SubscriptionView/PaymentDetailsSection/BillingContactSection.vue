<template>
  <div class="flex justify-between mt-10">
    <h2 class="font-semibold text-xl">Billing Contact</h2>
    <a
      href=""
      class="text-green-500 hover:underline px-2 py-1"
    >
      Edit
    </a>
  </div>
  <div class="mt-2 line-clamp-1 text-gray-500">
    {{
      appStore.activeCompany?.billingContact
        ? appStore.activeCompany?.billingContact
        : 'No Billing Contact'
    }}
  </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
</script>
