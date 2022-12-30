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
import { EventsSection, TabsPropType } from '@/components/resources/events/EventTabs/EventsSection'
import LivePreviewSection from '@/components/resources/session/LivePreviewSection.vue'
import ActiveNetworkRequestResources from '@/components/pages/projects/ProjectSessionView/ActiveNetworkRequestResources.vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'

const sessionStore = useSessionsStore()

const isLoadingSession = ref(true)

const route = useRoute()

const tabs = computed<TabsPropType>(() => [
  {
    type: EventTypesEnum.NETWORK_REQUEST,
    'data-cy': 'project-session-view__activate-network-events-tab',
    icon: 'pi pi-desktop',
    title: `Network (${sessionStore.networkEvents.length})`
  },
  {
    type: EventTypesEnum.CHANGED_URL,
    'data-cy': 'project-session-view__activate-change-url-events-tab',
    icon: 'pi pi-at',
    storeGetter: 'changedUrlEvents',
    title: `URL (${sessionStore.changedUrlEvents.length})`
  },
  {
    type: EventTypesEnum.ELEMENT_CLICKED,
    'data-cy': 'project-session-view__activate-click-events-tab',
    icon: 'pi pi-eye',
    title: `Click (${sessionStore.clickEvents.length})`
  },
  {
    type: EventTypesEnum.SCROLL,
    'data-cy': 'project-session-view__activate-scroll-events-tab',
    icon: 'pi pi-arrows-v',
    title: `Scroll (${sessionStore.scrollEvents.length})`
  },
  {
    type: EventTypesEnum.RESIZE_SCREEN,
    'data-cy': 'project-session-view__activate-resize-events-tab',
    icon: 'pi pi-arrows-h',
    title: `Resize (${sessionStore.resizedScreenEvents.length})`
  },
  {
    type: EventTypesEnum.DATABASE_TRANSACTION,
    'data-cy': 'project-session-view__activate-db-transaction-events-tab',
    icon: 'pi pi-server',
    title: `Database (${sessionStore.databaseTransactionEvents.length})`
  },
  {
    type: EventTypesEnum.LOG,
    'data-cy': 'project-session-view__activate-log-events-tab',
    icon: 'pi pi-paperclip',
    title: `Logs (${sessionStore.logEvents.length})`
  },
  {
    type: EventTypesEnum.CUSTOM_EVENT,
    'data-cy': 'project-session-view__activate-custom-events-tab',
    icon: 'pi pi-paperclip',
    title: `Custom (${sessionStore.customEvents.length})`
  }
])

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
