<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">🎥 Lets create your first recording</div>

      <a
        data-cy="onboarding-session-page__see-docs"
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
          href="https://docs.devqaly.com/getting-started/quick-start/"
          class="underline"
          target="_blank"
        >
          Devqaly's SDK</a
        >, you can create your first session. After you finish your first recording, you will be
        able to see it here.

        <div class="text-xs text-slate-500">Sessions can take up to 5 minutes to be converted.</div>
      </div>

      <div
        data-cy="onboarding-session-page__helper-record-session"
        class="border border-slate-200 rounded-lg aspect-video flex flex-col overflow-hidden relative"
        v-if="!hasFirstSessionRecorded"
      >
        <div class="bg-blue-400 text-center text-white font-semibold font-mono shadow px-5 py-2">
          You'll be able to see the button on the right bottom corner of your app to record a
          session
        </div>
        <div
          class="absolute bottom-5 right-5 bg-slate-100 border-red-400 border px-5 py-2 rounded-full animate-pulse font-mono font-semibold cursor-pointer"
        >
          Start Recording
        </div>

        <div class="flex gap-2 h-full p-2">
          <Skeleton
            height="100%"
            animation="none"
            width="20%"
          />

          <div class="grow">
            <Skeleton
              animation="none"
              height="70px"
              width="100%"
            />
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-5 max-h-[20rem] overflow-y-auto">
        <div
          v-for="session in sessionsResponse.data"
          data-cy="onboarding-session-page__session-row"
          :data-session-id="session.id"
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

      <div class="flex justify-between !mt-4">
        <RouterLink
          data-cy="onboarding-session-page__skip-step"
          :to="{ name: 'onboardInviteTeamMembers', params: route.params }"
        >
          <Button
            link
            class="!text-slate-400"
            label="Skip"
          />
        </RouterLink>

        <RouterLink
          data-cy="onboarding-session-page__next-step"
          :to="{ name: 'onboardInviteTeamMembers', params: route.params }"
        >
          <Button
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
import { emptyPagination, OrderBy } from '@/services/api'
import type { PaginatableRecord } from '@/services/api'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { useToast } from 'primevue/usetoast'
import { getProjectSessions } from '@/services/api/resources/project/actions'
import { useSessionsStore } from '@/stores/sessions'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { isVideoConverted } from '@/services/resources/SessionsService'

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
  fetchSessionsClear = setInterval(fetchSessions, 10000)
})

onUnmounted(() => {
  sessionStore.activeSession = sessionsCodecFactory()
  clearInterval(fetchSessionsClear)
})
</script>
