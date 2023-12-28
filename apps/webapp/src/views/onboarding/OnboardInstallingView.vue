<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">
        👋 Hello there Bruno Francisco. Lets setup your
        <span class="underline font-semibold">Bruno's Project</span>
      </div>

      <div>
        <Button link> See Docs </Button>
      </div>
    </div>

    <div class="mt-2 px-5 pt-2 pb-4">
      <div class="mb-4 flex items-center gap-2 align-middle font-semibold">
        <span
          class="bg-black p-2 rounded-full text-white w-[24px] h-[24px] text-center inline-block leading-[10px]"
          >1</span
        >
        Install Devqaly's SDK:
      </div>

      <div class="flex gap-4 mt-2">
        <div
          :class="{
            'font-semibold border-b border-transparent cursor-pointer px-2': true,
            '!border-b-blue-500': registryPicker === 'npm'
          }"
          @click="onChooseRegistryPicker('npm')"
        >
          NPM
        </div>
        <div
          :class="{
            'font-semibold border-b border-transparent cursor-pointer px-2': true,
            '!border-b-blue-500': registryPicker === 'yarn'
          }"
          @click="onChooseRegistryPicker('yarn')"
        >
          Yarn
        </div>
      </div>

      <DCode>{{ installCode }}</DCode>

      <div class="mb-4 flex items-center gap-2 align-middle font-semibold mt-8">
        <span
          class="bg-black p-2 rounded-full text-white w-[24px] h-[24px] text-center inline-block leading-[10px]"
          >2</span
        >
        Initiate the script
      </div>

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
        <pre
          class="bg-slate-500 text-white rounded-md"
          style="tab-size: 6; white-space-collapse: preserve-breaks"
        >
      <code>
    <!--   prettier-ignore -->
    import { DevqalySDK } from '@devqaly/browser'

    const devqaly = new DevqalySDK({
          projectKey: '...'
    })

    devqaly.showRecordingButton()
      </code>
    </pre>
      </template>

      <template v-if="renderStrategy === 'ssr'">
        <pre
          class="bg-slate-500 text-white rounded-md"
          style="tab-size: 6; white-space-collapse: preserve-breaks"
        >
      <code>
    <!--   prettier-ignore -->
    import { DevqalySDK } from '@devqaly/browser'

    const devqaly = new DevqalySDK({
          projectKey: '...'
    })

    function App() {
      useEffect(() => {
        devqaly.showRecordingButton()
      }, [])
    }
      </code>
    </pre>
      </template>

      <div class="flex justify-end">
        <Button class="!font-semibold !mt-4">
          Create First Session <i class="pi pi-chevron-right ml-2" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import DCode from '@/components/DCode.vue'

type Register = 'npm' | 'yarn'

type RenderStrategy = 'ssr' | 'spa'

const registryPicker = ref<Register>('npm')

const installCode = computed(() =>
  registryPicker.value === 'npm' ? 'npm install @devqaly/browser' : 'yarn add @devqaly/browser'
)

const renderStrategy = ref<RenderStrategy>('spa')

function onChooseRegistryPicker(register: Register) {
  registryPicker.value = register
}

function onSelectRenderStrategy(strategy: RenderStrategy) {
  renderStrategy.value = strategy
}
</script>
