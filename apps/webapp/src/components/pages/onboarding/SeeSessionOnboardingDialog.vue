<template>
  <Dialog
    data-cy="onboarding-session-page__see-session-dialog"
    ref="dialog"
    :style="{ maxWidth: '90%', minWidth: '500px' }"
    :visible="visible"
    :draggable="false"
    modal
    header="See your first session"
    @update:visible="onHide"
  >
    <div class="grid grid-cols-12 gap-5">
      <div class="col-span-2">
        <SessionSummary :session="session" />
      </div>

      <div class="col-span-7">
        <VideoSection
          :session="session"
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
      :key="sessionStore.activeSession.id"
      :tabs="tabs"
      :active-event-details="sessionStore.activeEventDetails"
      class="mt-2"
      v-if="isVideoConverted(sessionStore.activeSession.videoStatus)"
    />
  </Dialog>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue'
import type { PropType } from 'vue'
import Dialog from 'primevue/dialog'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import SessionSummary from '@/components/resources/session/SessionSummary.vue'
import VideoSection from '@/components/resources/session/VideoSection.vue'
import { useSessionsStore } from '@/stores/sessions'
import { isVideoConverted } from '@/services/resources/SessionsService'
import throttle from 'lodash.throttle'
import LivePreviewSection from '@/components/resources/session/LivePreviewSection.vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { emptyPagination } from '@/services/api'
import { EventsSection } from '@/components/resources/events/EventTabs/EventsSection'
import { useEventTabs } from '@/composables/useEventTabs'

const dialog = ref<InstanceType<typeof Dialog> | null>()

const sessionStore = useSessionsStore()

const { tabs } = useEventTabs()

defineProps({
  visible: {
    type: Boolean,
    required: true
  },
  session: {
    type: Object as PropType<SessionCodec>,
    required: true
  }
})

const emit = defineEmits(['hide'])

const onVideoUpdate: (duration: number) => void = throttle(async function (duration: number) {
  await sessionStore.getActiveSessionEventsForPartition(duration)
}, 500)

function onHide(value: boolean) {
  emit('hide', value)
}

function onVideoTimeUpdate(e: HTMLVideoElement) {
  sessionStore.currentVideoDuration = e.currentTime
}

function onUpdateActiveEventDetails(event: EventCodec) {
  sessionStore.activeEventDetails = event
}

watch(() => sessionStore.currentVideoDuration, onVideoUpdate)

onMounted(async () => {
  sessionStore.createVideoPartitionsForActiveSession()

  if (isVideoConverted(sessionStore.activeSession.videoStatus)) {
    await sessionStore.getActiveSessionEventsForPartition(0)
  }
})

onUnmounted(() => {
  sessionStore.currentVideoDuration = 0
  sessionStore.videoPartitions = {}
  sessionStore.activeSessionEventsRequest = emptyPagination()
})
</script>
