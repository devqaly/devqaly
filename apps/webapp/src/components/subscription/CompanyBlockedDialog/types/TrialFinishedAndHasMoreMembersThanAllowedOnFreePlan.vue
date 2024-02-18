<template>
  <div class="p-3 bg-red-50 text-red-500 border border-red-500 rounded-lg">
    <span class="font-semibold">
      {{ reason.description }} </span
    >. You can remove members at the
    <RouterLink
      class="underline"
      :to="{ name: 'listCompanyMembers', params: { companyId: company.id } }"
    >
      members page
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import type { BlockedReason, CompanyCodec } from '@/services/api/resources/company/codec'

defineProps<{
  reason: BlockedReason
  company: CompanyCodec
}>()
</script>
