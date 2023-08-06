<template>
  <div class="h-screen w-screen grid flex-column justify-content-center align-items-center">
    Assigning session to your account

    <ProgressSpinner class="mt-2" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { assignSessionToUser } from '@/services/api/resources/session/actions'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useAppStore } from '@/stores/app'

const toast = useToast()

const route = useRoute()

const router = useRouter()

const appStore = useAppStore()

onMounted(async () => {
  try {
    const response = await assignSessionToUser(route.params.sessionId as string, {
      userId: appStore.loggedUser.id
    })

    await router.push({
      name: 'projectSession',
      params: {
        projectId: response.data.data.project!.id,
        sessionId: route.params.sessionId as string
      }
    })
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error assigning session to you',
      detail:
        'There was an error assigning this session to you. We will redirect you 10 seconds and this session will be unassigned',
      life: 15000,
      group: 'bottom-center'
    })
  }
})
</script>
