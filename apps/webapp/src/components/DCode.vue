<template>
  <div class="relative">
    <div
      data-vitest="d-code__copy"
      class="top-[10px] right-[10px] absolute text-blue-400 p-2 rounded-md hover:bg-blue-200 hover:text-blue-500 transition-all cursor-pointer"
      v-text="copyButtonText"
      @click="onCopyClick"
    />

    <pre
      v-highlightjs
      class="rounded-md"
    ><code :class='language' ref='codeContent'><slot></slot></code></pre>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'primevue/usetoast'

const codeContent = ref<HTMLElement | null>(null)

const copyButtonText = ref<'Copy' | 'Copied'>('Copy')

defineProps({
  language: {
    type: String,
    default: 'javascript'
  }
})

const toast = useToast()

function onCopyClick() {
  if (codeContent.value === null) {
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
    .writeText(codeContent.value!.innerText)
    .then(() => {
      copyButtonText.value = 'Copied'

      setTimeout(() => {
        copyButtonText.value = 'Copy'
      }, 3000)
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

<style scoped>
code.hljs {
  background: #f1f5f9;
  padding: 16px;
  border-radius: 8px;
}
</style>
