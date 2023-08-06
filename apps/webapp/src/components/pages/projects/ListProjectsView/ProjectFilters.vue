<template>
  <div class="flex">
    <span class="p-input-icon-left">
      <i class="pi pi-search" />
      <InputText
        data-cy="list-projects-view__title-filter"
        type="text"
        placeholder="Search by title"
        v-model="_title"
      />
    </span>
  </div>
</template>

<script setup lang="ts">
import type { GetProjectsParameters } from '@/services/api/resources/project/codec'
import { computed } from 'vue'
import debounce from 'lodash.debounce'

const props = defineProps<{
  title: GetProjectsParameters['title']
}>()

const emit = defineEmits(['update:filters'])

const emitDebounced = debounce(emit, 500)

const _title = computed<string | undefined>({
  get: () => props.title,
  set: (title: string | undefined) => {
    emitDebounced('update:filters', { title })
  }
})
</script>
