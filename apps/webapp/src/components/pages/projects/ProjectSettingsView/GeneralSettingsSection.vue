<template>
  <section class="border-round-md border-1 surface-border">
    <div class="p-3 border-bottom-1 surface-border surface-ground">General Settings</div>

    <div class="p-2">
      <div class="flex align-items-center justify-content-between">
        <div class="col-6">
          <span class="font-medium">Project Name</span>
          <div class="text-gray-500 text-xs mt-1">
            The current name of the project.
            <span class="font-bold">Currently it is not possible to change the name.</span>
          </div>
        </div>
        <div class="col-6">
          <div class="p-inputgroup flex-1">
            <InputText
              class="surface-ground"
              :value="projectStore.activeProject!.title"
              id="title"
              type="text"
              readonly
              disabled
              aria-describedby="title-help"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="p-2 border-top-1 surface-border">
      <div class="flex align-items-center justify-content-between">
        <div class="col-6">
          <span class="font-medium">Project Key</span>
          <div class="text-gray-500 text-xs mt-1">
            Use this key when implementing our Javascript SDK in your web application.
            <a
              class="block"
              href="https://docs.devqaly.com"
              target="_blank"
            >
              Read our docs
            </a>
          </div>
        </div>
        <div class="col-6">
          <div class="p-inputgroup flex-1">
            <InputText
              class="surface-ground"
              :value="projectStore.activeProject!.projectKey"
              id="title"
              type="text"
              readonly
              disabled
              aria-describedby="title-help"
            />

            <Button
              class="surface-ground surface-border border-left-0 text-black-alpha-90"
              icon="pi pi-copy"
              severity="warning"
              v-tooltip.left="'Copy'"
              @click="onCopyProjectKeyClick"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useProjectsStore } from '@/stores/projects'
import { assertIsProjectCodec } from '@/services/resources/Project'
import { copyToClipboard } from '@/services/ui'
import { useToast } from 'primevue/usetoast'

const projectStore = useProjectsStore()

const toast = useToast()

function onCopyProjectKeyClick() {
  assertIsProjectCodec(projectStore.activeProject)

  try {
    copyToClipboard(projectStore.activeProject.projectKey)

    toast.add({
      severity: 'success',
      summary: 'Copied successfully',
      detail: 'Successfully copied project key to clipboard',
      life: 3000
    })
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error Copying',
      detail: 'There was an error copying the project key',
      life: 3000
    })
  }
}
</script>
