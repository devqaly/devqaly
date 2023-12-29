<template>
  <div class="pl-5 pr-5 pb-5 mt-4">
    <ActiveNetworkRequestResources />

    <div class="grid grid-cols-12 gap-5">
      <div class="col-span-2">
        <SessionSummary
          class="mb-5"
          :session="sessionStore.activeSession"
          :fetching-session="isLoadingSession"
        />
      </div>
      <div class="col-span-7">
        <div v-if="shouldShowVideoColumn">
          <VideoSection @update:videoTime="onVideoUpdate" />
        </div>

        <div
          class="flex flex-col items-center justify-center"
          data-cy="project-session-view__video-being-converted-info"
          v-else
        >
          <Image
            src="/images/illustrations/record-screen.png"
            alt="Image"
            image-class="w-full"
          />

          <div class="text-2xl font-semibold">
            We are currently processing your session, please be patient.
          </div>
        </div>
      </div>
      <div class="col-span-3">
        <LivePreviewSection />
      </div>
    </div>

    <EventsSection
      data-cy="project-session-view__bottom-events-section"
      :key="sessionStore.activeSession.id"
      class="mt-2"
      v-if="isVideoConverted(sessionStore.activeSession.videoStatus)"
    />
  </div>
</template>

<script lang="ts" setup>
import { useSessionsStore } from '@/stores/sessions'
import { computed, onBeforeMount, onBeforeUnmount, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import SessionSummary from '@/components/resources/session/SessionSummary.vue'
import VideoSection from '@/components/pages/projects/ProjectSessionView/VideoSection.vue'
import { isVideoConverted } from '@/services/resources/SessionsService'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { emptyPagination } from '@/services/api'
import throttle from 'lodash.throttle'
import { EventsSection } from '@/components/pages/projects/ProjectSessionView/EventTabs/EventsSection'
import LivePreviewSection from '@/components/pages/projects/ProjectSessionView/LivePreviewSection.vue'
import ActiveNetworkRequestResources from '@/components/pages/projects/ProjectSessionView/ActiveNetworkRequestResources.vue'

const sessionStore = useSessionsStore()

const isLoadingSession = ref(true)

const route = useRoute()

const shouldShowVideoColumn = computed(
  () => !isLoadingSession.value && isVideoConverted(sessionStore.activeSession.videoStatus)
)

onBeforeMount(async () => {
  isLoadingSession.value = true

  await sessionStore.getActiveSession(route.params.sessionId as string)

  if (isVideoConverted(sessionStore.activeSession.videoStatus)) {
    await sessionStore.getActiveSessionEventsForPartition(0)
  }

  isLoadingSession.value = false
})

onBeforeUnmount(() => {
  sessionStore.activeSession = sessionsCodecFactory()
  sessionStore.activeSessionEventsRequest = emptyPagination()
  sessionStore.currentVideoDuration = 0
})

const onVideoUpdate: (duration: number) => void = throttle(async function (duration: number) {
  sessionStore.getActiveSessionEventsForPartition(duration)
}, 500)

watch(() => sessionStore.currentVideoDuration, onVideoUpdate)
</script>
