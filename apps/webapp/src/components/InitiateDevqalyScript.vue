<template>
  <div>
    <div class="flex gap-4 mt-2">
      <div
        :class="{
          'font-semibold border-b border-transparent cursor-pointer px-2': true,
          '!border-b-blue-500': renderStrategy === 'spa'
        }"
        @click="onSelectRenderStrategy('spa')"
      >
        Single Page Application (SPA)
      </div>
      <div
        :class="{
          'font-semibold border-b border-transparent cursor-pointer px-2': true,
          '!border-b-blue-500': renderStrategy === 'ssr'
        }"
        @click="onSelectRenderStrategy('ssr')"
      >
        Server Side Rendered (SSR)
      </div>
    </div>

    <template v-if="renderStrategy === 'spa'">
      <!--   prettier-ignore -->
      <DCode class='mt-4'>import { DevqalySDK } from '@devqaly/browser'

const devqaly = new DevqalySDK({
  projectKey: '{{ projectId }}'
})

devqaly.showRecordingButton()</DCode>
    </template>

    <template v-if="renderStrategy === 'ssr'">
      <!--   prettier-ignore -->
      <DCode class='mt-4'>import { DevqalySDK } from '@devqaly/browser'<br>
const devqaly = new DevqalySDK({
  projectKey: '{{ projectId }}'
})

function App() {
  useEffect(() => {
      devqaly.showRecordingButton()
  }, [])
}</DCode>
    </template>
  </div>
</template>

<script setup lang="ts">
import DCode from '@/components/DCode.vue'
import { ref } from 'vue'

type RenderStrategy = 'ssr' | 'spa'

defineProps({
  projectId: {
    type: String,
    default: '...'
  }
})

const renderStrategy = ref<RenderStrategy>('spa')

function onSelectRenderStrategy(strategy: RenderStrategy) {
  renderStrategy.value = strategy
}
</script>
