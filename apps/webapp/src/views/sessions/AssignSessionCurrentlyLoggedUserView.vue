<template>
  <div
    class="h-screen w-screen grid flex-column justify-center items-center"
    v-if="isFetchingSessionInformation"
  >
    <Card class="shadow-md h-fit flex flex-col p-5 mx-auto text-slate-500">
      Fetching information about your session

      <ProgressSpinner
        class="!mt-4"
        style="width: 50px; height: 50px"
        stroke-width="2"
      />
    </Card>
  </div>

  <div
    class="h-screen w-screen grid flex-column justify-center items-center"
    v-if="isAssigningSessionToUser"
  >
    <Card class="shadow-md h-fit flex flex-col p-5 mx-auto text-center text-slate-500">
      Assigning session to your account

      <ProgressSpinner
        class="!mt-4"
        style="width: 50px; height: 50px"
        stroke-width="2"
      />
    </Card>
  </div>

  <div
    v-else
    class="h-screen w-screen grid flex-column content-center items-center"
  >
    <Card class="w-1/2 shadow-md h-fit flex flex-col p-5 mx-auto max-w-[450px] text-center">
      <h1 class="text-3xl mb-2">Session Created</h1>

      <span class="text-slate-500">
        You can share this session with a developer by copying the link below. <br />
      </span>

      <Button
        label="Copy Session URL"
        class="w-full !mt-4"
        outlined
        @click="copySessionUrl"
      />

      <span class="text-xs text-slate-500 text-center">
        You will have to ask a team member to be invited to the project in order to claim this
        session.
      </span>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { assignSessionToUser, getSession } from '@/services/api/resources/session/actions'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useAppStore } from '@/stores/app'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'

const toast = useToast()

const route = useRoute()

const router = useRouter()

const appStore = useAppStore()

const isAssigningSessionToUser = ref(false)

const session = ref(sessionsCodecFactory())

const isFetchingSessionInformation = ref(true)

async function assignSessionToLoggedUser() {
  if (!appStore.isAuthenticated) return

  if (!session.value.project) {
    throw new Error('`session.value.project` should be defined')
  }

  if (!session.value.project.company) {
    throw new Error('`session.value.project.company` should be defined')
  }

  try {
    isAssigningSessionToUser.value = true

    const response = await assignSessionToUser(route.params.sessionId as string, {
      userId: appStore.loggedUser.id
    })

    await router.push({
      name: 'projectSession',
      params: {
        projectId: response.data.data.project!.id,
        sessionId: route.params.sessionId as string,
        companyId: session.value.project.company.id
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
}

async function fetchSessionInformation() {
  try {
    isFetchingSessionInformation.value = true

    const response = await getSession(route.params.sessionId as string)

    session.value = response.data.data
    isFetchingSessionInformation.value = false
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error fetching session',
      detail: 'There was an error fetching session information',
      life: 15000,
      group: 'bottom-center'
    })
  }
}

function copySessionUrl() {
  if (!session.value.project) {
    throw new Error('`session.value.project` should be defined')
  }

  if (!session.value.project.company) {
    throw new Error('`session.value.project.company` should be defined')
  }

  const url = router.resolve({
    name: 'projectSession',
    params: {
      projectId: session.value.project.id,
      sessionId: route.params.sessionId as string,
      companyId: session.value.project.company.id
    }
  })

  if (!navigator.clipboard) {
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
    .writeText(new URL(url.href, window.location.origin).href)
    .then(() => {
      toast.add({
        severity: 'success',
        summary: 'Copied successfully',
        detail: 'Successfully copied content to clipboard',
        life: 3000
      })
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

onMounted(async () => {
  await fetchSessionInformation()
  await assignSessionToLoggedUser()
})
</script>
