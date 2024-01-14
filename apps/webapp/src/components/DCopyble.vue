<template>
  <div
    class="p-2 rounded-md text-white !relative py-2"
    style="background-color: rgba(37, 42, 55) !important; min-height: 32px"
  >
    <Button
      style="top: 5px; right: 5px"
      data-vitest="d-copyble__copy"
      class="!absolute right-0"
      severity="secondary"
      text
      icon="pi pi-copy"
      icon-class="text-black"
      rounded
      :pt="{
        root: {
          style: 'height: 20px; width: 20px; background-color: white; padding: 5px !important'
        }
      }"
      @click="onCopyClick"
    />

    <slot></slot>
  </div>
</template>

<script lang="ts" setup>
import { useToast } from 'primevue/usetoast'

const props = defineProps<{
  content: string | number
}>()

const toast = useToast()

function onCopyClick() {
  if (!navigator.clipboard) {
    toast.add({
      severity: 'error',
      summary: 'Error Copying',
      detail: 'Your browser do not support navigator.clipboard',
      life: 3000,
      group: 'bottom-center'
    })

    return
  }

  navigator.clipboard
    .writeText(props.content.toString())
    .then(() => {
      toast.add({
        severity: 'success',
        summary: 'Copied successfully',
        detail: 'Successfully copied content to clipboard',
        life: 3000
      })
    })
    .catch(() => {
      toast.add({
        severity: 'error',
        summary: 'Error Copying',
        detail: 'There was an error copying the content',
        life: 3000
      })
    })
}
</script>
