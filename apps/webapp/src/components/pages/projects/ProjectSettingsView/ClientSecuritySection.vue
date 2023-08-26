<template>
  <section class="border-round-md border-1 surface-border">
    <div class="p-3 border-bottom-1 surface-border surface-ground">Client Security</div>
    <div class="p-2">
      <div class="flex align-items-center justify-content-between">
        <div class="col-6">
          <span class="font-medium">Security Token</span>
          <div class="text-gray-500 text-xs mt-1">
            The security token is used to identify your backend when sending events to Devqaly's
            server.
            <div class="font-bold">This is a secret and should not be publicly available.</div>
          </div>
        </div>
        <div class="col-6">
          <div class="p-inputgroup flex-1">
            <ConfirmDialog
              class="max-w-29rem"
              :draggable="false"
            />

            <InputText
              class="surface-ground"
              :value="projectStore.activeProject && projectStore.activeProject.securityToken"
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
              @click="onCopyClick"
            />
            <Button
              icon="pi pi-refresh"
              v-tooltip.left="'Refresh Token'"
              :loading="isRefreshingSecurityToken"
              @click="onRefreshSecurityTokenClick"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup lang="ts">
import { useProjectsStore } from '@/stores/projects'
import { ref } from 'vue'
import { updateProjectSecurityToken } from '@/services/api/resources/project/actions'
import { assertIsProjectCodec } from '@/services/resources/Project'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { copyToClipboard } from '@/services/ui'

const projectStore = useProjectsStore()

const isRefreshingSecurityToken = ref(false)

const confirm = useConfirm()

const toast = useToast()

async function onRefreshSecurityTokenClick() {
  confirm.require({
    message:
      'Revoking the current security token will not allow events created by your backend to be created anymore UNTIL you update the security token in your codebase. Are you sure?',
    header: 'Destructive action',
    acceptLabel: 'Yes, revoke current security token',
    accept: refreshSecurityToken,
    blockScroll: true
  })
}

async function refreshSecurityToken() {
  assertIsProjectCodec(projectStore.activeProject)

  try {
    isRefreshingSecurityToken.value = true

    const response = await updateProjectSecurityToken(projectStore.activeProject.id)

    projectStore.activeProject.securityToken = response.data.data.securityToken
  } finally {
    isRefreshingSecurityToken.value = false
  }
}

function onCopyClick() {
  assertIsProjectCodec(projectStore.activeProject)

  try {
    copyToClipboard(projectStore.activeProject.securityToken)

    toast.add({
      severity: 'success',
      summary: 'Copied successfully',
      detail: 'Successfully copied security token to clipboard',
      life: 3000
    })
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error Copying',
      detail: 'There was an error copying the security token',
      life: 3000
    })
  }
}
</script>
