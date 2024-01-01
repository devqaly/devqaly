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
      v-if="isVideoConverted(sessionStore.activeSession.videoStatus)"
      :session="sessionStore.activeSession"
      :visible="isDialogOpen"
      @hide="(value: boolean) => isDialogOpen = value"
    />

    <div class="mt-2 px-5 pt-2 pb-4">
      <div class="mb-4">
        Now that you have installed
        <a
          href="https://docs.devqaly.com"
          class="underline"
          target="_blank"
        >
          Devqaly's SDK </a
        >, you can create your first session. After you finish your first recording, you will be
        able to see it here.

        <div class="text-xs text-slate-500">Sessions can take up to 5 minutes to be converted.</div>
      </div>

      <div
        class="border border-slate-200 rounded-lg aspect-video flex flex-col overflow-hidden relative"
        v-if="!hasFirstSessionRecorded"
      >
        <div class="bg-blue-400 text-center text-white font-semibold font-mono shadow">
          You'll be able to see the button on the right bottom corner to record a session
        </div>
        <div
          class="absolute bottom-5 right-5 bg-slate-100 border-red-400 border px-5 py-2 rounded-full animate-pulse font-mono font-semibold cursor-pointer"
        >
          Start Recording
        </div>

        <div class="p-2 flex-col flex gap-5 mt-2">
          <div
            class="flex gap-5"
            v-for="row in rows"
            :key="`row-${row}`"
          >
            <div
              v-for="column in columns"
              :key="`column-${column}`"
              class="bg-slate-200 rounded-lg aspect-square w-2/12"
            />
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-5 max-h-[20rem] overflow-y-auto">
        <div
          v-for="session in sessionsResponse.data"
          :key="`session-card-${session.id}`"
          class="flex justify-between items-center gap-2 bg-white p-4 shadow rounded cursor-pointer border border-transparent hover:border-blue-400 transition-all"
          @click="() => onShowSessionClick(session)"
        >
          <div class="text-xl font-semibold">{{ session.platformName }} session</div>
          <div class="p-4 rounded-full bg-blue-400 w-10 h-10 flex items-center justify-center">
            <i class="pi pi-play text-white"></i>
          </div>
        </div>
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
import { computed, onMounted, onUnmounted, ref } from 'vue'
import SeeSessionOnboardingDialog from '@/components/pages/onboarding/SeeSessionOnboardingDialog.vue'
import { emptyPagination, OrderBy, PaginatableRecord } from '@/services/api'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { useToast } from 'primevue/usetoast'
import { getProjectSessions } from '@/services/api/resources/project/actions'
import { useSessionsStore } from '@/stores/sessions'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { isVideoConverted } from '@/services/resources/SessionsService'
import { range } from '@/services/number'

const columns = range(1, 6)

const rows = range(1, 4)

const route = useRoute()

const isDialogOpen = ref(false)

const sessionStore = useSessionsStore()

const sessionsResponse = ref(emptyPagination<SessionCodec>())

const hasFirstSessionRecorded = computed(() =>
  sessionsResponse.value.data.find((e) => isVideoConverted(e.videoStatus))
)

const toast = useToast()

let fetchSessionsClear: ReturnType<typeof setInterval>

async function fetchSessions() {
  try {
    const response = await getProjectSessions(route.params.projectId as string, {
      createdAtOrder: OrderBy.DESC
    })

    sessionsResponse.value = response.data as PaginatableRecord<SessionCodec>

    if (
      response.data.meta.total > 0 &&
      sessionsResponse.value.data.find((s) => isVideoConverted(s.videoStatus))
    ) {
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

function onShowSessionClick(session: SessionCodec) {
  sessionStore.activeSession = session
  isDialogOpen.value = true
}

onMounted(() => {
  fetchSessionsClear = setInterval(fetchSessions, 2000)
})

onUnmounted(() => {
  sessionStore.activeSession = sessionsCodecFactory()
})
</script>
