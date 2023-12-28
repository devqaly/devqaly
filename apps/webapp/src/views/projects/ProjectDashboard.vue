<template>
  <div class="p-4">
    To allow your Quality Assurance Engineers to start recording their sessions, you will need to
    follow the following steps.

    <div class="mb-4 flex items-center gap-2 align-middle font-semibold mt-4">
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

    <InitiateDevqalyScript
      :project-id="projectStore.activeProject ? projectStore.activeProject.projectKey : '...'"
    />
  </div>
</template>
<script setup lang="ts">
import DCode from '@/components/DCode.vue'
import { useProjectsStore } from '@/stores/projects'
import InstallDevqaly from '@/components/InstallDevqaly.vue'
import InitiateDevqalyScript from '@/components/InitiateDevqalyScript.vue'

const projectStore = useProjectsStore()
</script>
