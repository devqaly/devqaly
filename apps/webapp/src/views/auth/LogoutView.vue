<template>
  <div class="h-screen flex justify-center items-center bg-slate-100">
    <div class="bg-white p-4 shadow-md rounded-lg w-full lg:max-w-[450px]">
      <div class="text-center mb-4">Logging you out</div>
      <ProgressSpinner class="!block" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'

const appStore = useAppStore()

const router = useRouter()

const toast = useToast()

onMounted(async () => {
  await appStore.logoutUser()

  toast.add({
    severity: 'success',
    summary: 'Logout',
    detail: 'You have been successfully logged out',
    life: 3000,
    group: 'bottom-center'
  })

  appStore.$reset()

  router.push({ name: 'authLogin' })
})
</script>
