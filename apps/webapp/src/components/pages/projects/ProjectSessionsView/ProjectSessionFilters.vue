<template>
  <div class="flex gap-2">
    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="project-sessions-view__created-by-filter"
        type="text"
        placeholder="Search by creator"
        :value="filters['createdByName']"
        @update:modelValue='(value: string | undefined) => updateFilter("createdByName", value)'
      />
    </span>

    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="project-sessions-view__os-filter"
        type="text"
        placeholder="OS"
        :value="filters['os']"
        @update:modelValue='(value: string | undefined) => updateFilter("os", value)'
      />
    </span>

    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="project-sessions-view__platform-filter"
        type="text"
        placeholder="Platform"
        :value="filters['platform']"
        @update:modelValue='(value: string | undefined) => updateFilter("platform", value)'
      />
    </span>

    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="project-sessions-view__version-filter"
        type="text"
        placeholder="Version"
        :value="filters['version']"
        @update:modelValue='(value: string | undefined) => updateFilter("version", value)'
      />
    </span>
  </div>
</template>

<script lang="ts" setup>
import debounce from 'lodash.debounce'
import type { GetProjectSessionsParameters } from '@/services/api/resources/project/codec'

const props = defineProps<{
  filters: GetProjectSessionsParameters
}>()

const emit = defineEmits(['update:filters'])

const emitDebounced = debounce(emit, 500)

function updateFilter(
  property: keyof GetProjectSessionsParameters,
  value: GetProjectSessionsParameters[typeof property]
) {
  emitDebounced('update:filters', {
    ...props.filters,
    [property]: value
  } as GetProjectSessionsParameters)
}
</script>
