<template>
  <Dialog
    data-cy="setup-sdk-dialog"
    v-model:visible="visible"
    modal
    header="Integration Details"
    :draggable="false"
    :style="{ maxWidth: '90%', minWidth: '500px' }"
  >
    <div class="mb-4 flex items-center gap-2 align-middle font-semibold">
      <span
        class="bg-black p-2 rounded-full text-white w-[24px] h-[24px] text-center inline-block leading-[10px]"
        >1</span
      >
      Install Devqaly's SDK:
    </div>

    <InstallDevqaly />

    <div class="mb-4 flex items-center gap-2 align-middle font-semibold mt-8">
      <span
        class="bg-black p-2 rounded-full text-white w-[24px] h-[24px] text-center inline-block leading-[10px]"
        >2</span
      >
      Initiate the script
    </div>

    <InitiateDevqalyScript :project-id="project ? project.projectKey : ''" />
  </Dialog>
</template>

<script setup lang="ts">
import type { ProjectCodec } from '@/services/api/resources/project/codec'
import { computed } from 'vue'
import InstallDevqaly from '@/components/InstallDevqaly.vue'
import InitiateDevqalyScript from '@/components/InitiateDevqalyScript.vue'

const props = defineProps<{
  project: ProjectCodec | null
}>()

const emit = defineEmits(['close'])

const visible = computed<boolean>({
  get: () => props.project !== null,
  set: () => emit('close')
})
</script>
