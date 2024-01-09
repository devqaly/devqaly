<template>
  <div data-cy="install-devqaly">
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

    <DCode
      class="mt-4"
      :key="`register-${registryPicker}`"
      >{{ installCode }}</DCode
    >
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import DCode from '@/components/DCode.vue'

type Register = 'npm' | 'yarn'

const registryPicker = ref<Register>('npm')

const installCode = computed(() =>
  registryPicker.value === 'npm' ? 'npm install @devqaly/browser' : 'yarn add @devqaly/browser'
)

function onChooseRegistryPicker(register: Register) {
  registryPicker.value = register
}
</script>
