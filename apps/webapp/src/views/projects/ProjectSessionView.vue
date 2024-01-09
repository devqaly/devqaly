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
        <VideoSection
          :session="sessionStore.activeSession"
          :is-loading-session="isLoadingSession"
          @update:videoTime="onVideoTimeUpdate"
        />
      </div>
      <div class="col-span-3">
        <LivePreviewSection
          :events="sessionStore.liveEvents"
          :session="sessionStore.activeSession"
          @update:activeEventDetails="onUpdateActiveEventDetails"
        />
      </div>
    </div>

    <EventsSection
      data-cy="project-session-view__bottom-events-section"
      :key="sessionStore.activeSession.id"
      :tabs="tabs"
      :active-event-details="sessionStore.activeEventDetails"
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
import VideoSection from '@/components/resources/session/VideoSection.vue'
import { isVideoConverted } from '@/services/resources/SessionsService'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { emptyPagination } from '@/services/api'
import throttle from 'lodash.throttle'
import { EventsSection } from '@/components/resources/events/EventTabs/EventsSection'
import LivePreviewSection from '@/components/resources/session/LivePreviewSection.vue'
import ActiveNetworkRequestResources from '@/components/pages/projects/ProjectSessionView/ActiveNetworkRequestResources.vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { useEventTabs } from '@/composables/useEventTabs'

const sessionStore = useSessionsStore()

const isLoadingSession = ref(true)

const route = useRoute()

const { tabs } = useEventTabs()

onBeforeMount(async () => {
  isLoadingSession.value = true

  await sessionStore.getActiveSession(route.params.sessionId as string)

  sessionStore.createVideoPartitionsForActiveSession()

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
  await sessionStore.getActiveSessionEventsForPartition(duration)
}, 500)

function onVideoTimeUpdate(e: HTMLVideoElement) {
  sessionStore.currentVideoDuration = e.currentTime
}

function onUpdateActiveEventDetails(event: EventCodec) {
  sessionStore.activeEventDetails = event
}

watch(() => sessionStore.currentVideoDuration, onVideoUpdate)
</script>
