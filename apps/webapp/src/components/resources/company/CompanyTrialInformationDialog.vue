<template>
  <Dialog
    data-cy="company-trial-information-dialog"
    v-model:visible="visible"
    modal
    :draggable="false"
    :style="{ maxWidth: '500px' }"
  >
    <template #header>
      <div class="inline-flex align-items-center justify-content-center gap-2 text-xl">
        🎉
        <span class="font-bold white-space-nowrap">
          You have a {{ NUMBER_DAYS_FOR_FREE_TRIAL }} days free trial
        </span>
      </div>
    </template>

    Take full advantage of Devqaly's features in this free trial:

    <ol class="flex flex-col gap-2">
      <li class="flex gap-2 mt-4">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Up to 3 projects
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Up to 100 sessions on each project
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Video duration up to 5 minutes
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Database transaction events
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Custom events
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Priority Support
      </li>
      <li class="flex gap-2">
        <div class="bg-green-50 px-2 py-0.5 rounded-full">
          <i class="pi pi-check text-green-500"></i>
        </div>
        Github connection (upcoming)
      </li>
    </ol>

    <Button
      label="Get Started"
      class="w-full !mt-4"
      @click="visible = false"
    />

    <div class="mt-2 text-sm text-gray-500 text-center">
      After your {{ NUMBER_DAYS_FOR_FREE_TRIAL }} day free trial ends, you can choose to subscribe
      to the pro version or continue using Devqaly for free, but with limited access.
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog'
import { NUMBER_DAYS_FOR_FREE_TRIAL } from '@/services/resources/Subscription'

const visible = defineModel<boolean>('visible', { required: true })
</script>
