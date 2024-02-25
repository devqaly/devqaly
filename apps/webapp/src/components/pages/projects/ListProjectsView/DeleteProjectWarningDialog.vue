<template>
  <Dialog
    data-cy="list-projects-view__delete-project-dialog"
    v-model:visible="isDialogOpen"
    modal
    :show-header="false"
    :draggable="false"
    :style="{ maxWidth: '580px' }"
    :pt="{ content: { class: 'rounded-lg !p-5' } }"
  >
    <h2 class="text-[3.4rem] font-bold text-center text-black">Project Deletion</h2>
    <div class="text-gray-400 text-[1.5rem] mt-1 text-center px-5">
      You are about to delete
      <strong data-cy="list-projects-view__delete-project-dialog-project-name">
        {{ projectStore.selectedProjectForDeletion?.title }}
      </strong>
      . You will lose all data related to it. This is irreversible. Are you sure?
    </div>

    <Button
      data-cy="list-projects-view__confirm-delete-project"
      class="!mt-4 w-full"
      label="Delete Project"
      :loading="isDeletingProject"
      @click="onConfirmDeleteProject"
    />

    <Button
      data-cy="list-projects-view__cancel-delete-project-dialog"
      class="!mt-2 !text-gray-500 w-full"
      label="Cancel"
      text
      :loading="isDeletingProject"
      @click="isDialogOpen = false"
    />
  </Dialog>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue'
import { useProjectsStore } from '@/stores/projects'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'

const projectStore = useProjectsStore()

const isDeletingProject = ref(false)

const isDialogOpen = computed({
  get: (): boolean => {
    return projectStore.selectedProjectForDeletion !== null
  },
  set: (value: boolean) => {
    if (value) return

    projectStore.selectedProjectForDeletion = null
  }
})

async function onConfirmDeleteProject() {
  if (projectStore.selectedProjectForDeletion === null) {
    throw new Error('`projectStore.selectedProjectForDeletion` should be defined')
  }

  try {
    isDeletingProject.value = true
    await projectStore.deleteProject(projectStore.selectedProjectForDeletion.id)
    projectStore.selectedProjectForDeletion = null
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isDeletingProject.value = false
  }
}
</script>
