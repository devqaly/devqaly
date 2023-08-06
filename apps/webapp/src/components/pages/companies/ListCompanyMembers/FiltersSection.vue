<template>
  <div class="flex gap-2">
    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="list-company-members__member-name-filter"
        type="text"
        placeholder="Member name"
        v-model="_memberName"
      />
    </span>

    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="list-company-members__invite-by-name-filter"
        type="text"
        placeholder="Invited by"
        v-model="_invitedByName"
      />
    </span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import debounce from 'lodash.debounce'
import type { GetCompanyMembersParameters } from '@/services/api/resources/company/companyMember/requests'

const props = defineProps<{
  filters: GetCompanyMembersParameters
}>()

const emit = defineEmits(['update:filters'])

const debouncedEmit = debounce(emit, 500)

const _invitedByName = computed<string | undefined>({
  get: () => props.filters.invitedByName,
  set: (invitedByName: string | undefined) =>
    debouncedEmit('update:filters', {
      ...props.filters,
      invitedByName
    } as GetCompanyMembersParameters)
})

const _memberName = computed<string | undefined>({
  get: () => props.filters.memberName,
  set: (memberName: string | undefined) =>
    debouncedEmit('update:filters', {
      ...props.filters,
      memberName
    } as GetCompanyMembersParameters)
})
</script>
