<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">🎥 Lets create your first recording</div>

      <a
        target="_blank"
        href="https://docs.devqaly.com/getting-started/introduction"
      >
        <Button link> See Docs </Button>
      </a>
    </div>

    <SeeSessionOnboardingDialog
      v-if="session"
      :session="session"
      :visible="isDialogOpen"
      @hide="(value: boolean) => isDialogOpen = value"
    />

    <div class="mt-2 px-5 pt-2 pb-4">
      Now that you have installed Devqaly's SDK, you can create your first session.

      <div
        class="bg-slate-100 w-full aspect-video flex justify-center items-center rounded-lg mt-2 text-xl"
        @click="onShowSessionClick"
      >
        Your session will show up here when we receive it
      </div>

      <div class="flex justify-end">
        <RouterLink :to="{ name: 'onboardInviteTeamMembers', params: route.params }">
          <Button
            class="!mt-4"
            label="Invite Team Members"
            icon="pi pi-chevron-right"
            icon-pos="right"
          />
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'
import { computed, onMounted, ref } from 'vue'
import SeeSessionOnboardingDialog from '@/components/pages/onboarding/SeeSessionOnboardingDialog.vue'
import { emptyPagination, OrderBy, PaginatableRecord } from '@/services/api'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { useToast } from 'primevue/usetoast'
import { getProjectSessions } from '@/services/api/resources/project/actions'

const route = useRoute()

const isDialogOpen = ref(false)

const sessionsResponse = ref(emptyPagination<SessionCodec>())

const toast = useToast()

const session = computed<SessionCodec | null>(() => sessionsResponse.value.data[0] ?? null)

let fetchSessionsClear: ReturnType<typeof setInterval>

async function fetchSessions() {
  try {
    const response = await getProjectSessions(route.params.projectId as string, {
      createdAtOrder: OrderBy.DESC
    })

    sessionsResponse.value = response.data as PaginatableRecord<SessionCodec>

    if (response.data.meta.total > 0) {
      clearInterval(fetchSessionsClear)
    }
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error fetching details about sessions',
      life: 3000,
      group: 'bottom-center'
    })
  }
}

function onShowSessionClick() {
  if (session.value === null) return

  isDialogOpen.value = true
}

onMounted(() => {
  fetchSessionsClear = setInterval(fetchSessions, 2000)
})
</script>
