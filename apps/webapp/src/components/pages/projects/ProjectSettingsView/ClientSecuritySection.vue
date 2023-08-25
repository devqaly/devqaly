<template>
  <section class="border-round-md border-1 surface-border">
    <div class="p-3 border-bottom-1 surface-border surface-ground">Client Security</div>
    <div class="p-4">
      <div class="flex align-items-center justify-content-between gap-3">
        <div class="col-6">
          <span class="font-medium">Security Token</span>
          <div class="text-gray-500 text-xs mt-1">
            The security token is used to identify your backend when sending events to Devqaly's
            server.
            <span class="font-bold">This is a secret and should not be publicly available.</span>
          </div>
        </div>
        <div class="col-6 flex justify-content-end">
          <div class="p-inputgroup flex-1">
            <InputText
              class="w-full surface-ground"
              :value="projectStore.activeProject && projectStore.activeProject.securityToken"
              id="title"
              type="text"
              readonly
              disabled
              aria-describedby="title-help"
            />
            <span
              class="p-inputgroup-addon cursor-pointer"
              v-tooltip.left="'Refresh Token'"
              @click="onRefreshSecurityTokenClick"
            >
              <i class="pi pi-refresh"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import { useProjectsStore } from '@/stores/projects'

const projectStore = useProjectsStore()

function onRefreshSecurityTokenClick() {
  alert('refresh token')
}
</script>
