<template>
  <RouterView v-if="!isLoading" />
  <div
    class="grid h-screen w-screen justify-content-center align-items-center"
    v-if="isLoading"
  >
    <ProgressSpinner />
  </div>

  <Toast
    group="bottom-center"
    position="bottom-center"
  />
  <Toast />
</template>

<script setup lang="ts">
import { RouterView, useRoute } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { onBeforeMount, ref } from 'vue'

const isLoading = ref(true)

const appStore = useAppStore()

const route = useRoute()

onBeforeMount(async () => {
  isLoading.value = true
  await appStore.onLoadApp()

  if (route.params.companyId) {
    appStore.activeCompany = appStore.loggedUserCompanies.data.find(
      (c) => c.id === route.params.companyId
    )!
  } else {
    appStore.activeCompany = appStore.loggedUserCompanies.data[0]
  }

  isLoading.value = false
})
</script>
