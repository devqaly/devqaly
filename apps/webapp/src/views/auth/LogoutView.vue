<template>
  <div class="h-screen flex justify-content-center align-items-center surface-ground">
    <div class="surface-card p-4 shadow-2 border-round w-full lg:w-4">
      <div class="text-center mb-4">Logging you out</div>
      <ProgressSpinner class="block" />
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
