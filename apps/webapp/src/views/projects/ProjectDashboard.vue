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

    <CompanyTrialInformationDialog
      v-model:visible="isShowingTrialWarning"
      @update:visible="onUpdateVisibleCompanyTrialDialog"
    />
  </div>
</template>
<script setup lang="ts">
import { useProjectsStore } from '@/stores/projects'
import InstallDevqaly from '@/components/InstallDevqaly.vue'
import InitiateDevqalyScript from '@/components/InitiateDevqalyScript.vue'
import CompanyTrialInformationDialog from '@/components/resources/company/CompanyTrialInformationDialog.vue'
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME } from '@/services/resources/Company'

const projectStore = useProjectsStore()

const route = useRoute()

const router = useRouter()

const isShowingTrialWarning = ref(route.query[SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME] !== undefined)

function onUpdateVisibleCompanyTrialDialog(isOpen: boolean) {
  if (isOpen) return

  router.replace({ name: route.name! })
}
</script>
