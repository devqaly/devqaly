<template>
  <Dialog
    data-cy="setup-sdk-dialog"
    v-model:visible="visible"
    modal
    header="How to setup your SDK"
    :draggable="false"
    :style="{ maxWidth: '90%', minWidth: '500px' }"
  >
    In order to setup our SDK you will need to first install it with

    <DCode>npm install @devqaly/browser</DCode>
    Or, if you use yarn
    <DCode>yarn add @devqaly/browser</DCode>

    Then you will have to initiate the SDK (usually in your <strong>main.ts</strong> OR
    <strong>main.js</strong>):

    <pre
      class="bg-slate-500 text-white p-5 rounded-md"
      style="tab-size: 6; white-space-collapse: preserve-breaks"
    >
        <!--   prettier-ignore -->
        import { DevqalySDK } from '@devqaly/browser'

        const devqaly = new DevqalySDK({
              projectKey: '{{project ? project.projectKey : ''}}'
        })

        devqaly.showRecordingButton()
    </pre>
  </Dialog>
</template>
<script setup lang="ts">
import DCode from '@/components/DCode.vue'
import type { ProjectCodec } from '@/services/api/resources/project/codec'
import { computed } from 'vue'

const props = defineProps<{
  project: ProjectCodec | null
}>()

const emit = defineEmits(['close'])

const visible = computed<boolean>({
  get: () => props.project !== null,
  set: () => emit('close')
})
</script>
