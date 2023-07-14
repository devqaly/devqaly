<template>
  <div class="p-4">
    To allow your Quality Assurance Engineers to start recording their sessions, firstly you will
    need to install our SDK in your frontend web application.

    <DCode>npm install @devqaly/browser</DCode>
    Or, if you use yarn
    <DCode>yarn add @devqaly/browser</DCode>

    Then you will have to initiate the SDK (usually in your <strong>main.ts</strong> OR
    <strong>main.js</strong>):

    <pre
      class="bg-black-alpha-90 text-white p-4 border-round-md"
      style="tab-size: 6; white-space-collapse: preserve-breaks"
    >
import { DevqalySDK } from '@devqaly/browser'

        const devqaly = new DevqalySDK({
              projectKey: '{{
        projectStore.activeProject ? projectStore.activeProject.projectKey : ''
      }}'
        })

        devqaly.showRecordingButton()
    </pre>
  </div>
</template>
<script setup lang="ts">
import DCode from '@/components/DCode.vue'
import { useProjectsStore } from '@/stores/projects'

const projectStore = useProjectsStore()
</script>
