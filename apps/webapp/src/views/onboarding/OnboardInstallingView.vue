<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">
        👋 Hello there Bruno Francisco. Lets setup your
        <span class="underline font-semibold">{{ projectStore.onboardingProject!.title }}</span>
      </div>

      <a
        target="_blank"
        href="https://docs.devqaly.com/getting-started/introduction"
      >
        <Button link> See Docs </Button>
      </a>
    </div>

    <div class="mt-2 px-5 pt-2 pb-4">
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

      <InitiateDevqalyScript :project-id="projectStore.onboardingProject!.projectKey" />

      <div class="flex justify-end">
        <RouterLink :to="{ name: 'onboardCreateSession', params: route.params }">
          <Button
            class="!mt-4"
            label="Create First Session"
            icon="pi pi-chevron-right"
            icon-pos="right"
          />
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'
import InstallDevqaly from '@/components/InstallDevqaly.vue'
import InitiateDevqalyScript from '@/components/InitiateDevqalyScript.vue'
import { useProjectsStore } from '@/stores/projects'

const route = useRoute()

const projectStore = useProjectsStore()
</script>
