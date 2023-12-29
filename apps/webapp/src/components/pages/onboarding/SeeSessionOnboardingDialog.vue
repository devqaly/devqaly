<template>
  <Dialog
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
  </Dialog>
</template>

<script setup lang="ts">
import { onMounted, PropType, ref, watch } from 'vue'
import Dialog from 'primevue/dialog'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import SessionSummary from '@/components/resources/session/SessionSummary.vue'
import VideoSection from '@/components/resources/session/VideoSection.vue'
import { useSessionsStore } from '@/stores/sessions'
import { isVideoConverted } from '@/services/resources/SessionsService'
import throttle from 'lodash.throttle'
import LivePreviewSection from '@/components/resources/session/LivePreviewSection.vue'
import { EventCodec } from '@/services/api/resources/session/events/codec'

const dialog = ref<InstanceType<typeof Dialog> | null>()

const sessionStore = useSessionsStore()

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

function createVideoPartitions() {
  sessionStore.createVideoPartitionsForActiveSession()
}

function onUpdateActiveEventDetails(event: EventCodec) {
  sessionStore.activeEventDetails = event
}

async function fetchEvents() {
  if (isVideoConverted(sessionStore.activeSession.videoStatus)) {
    await sessionStore.getActiveSessionEventsForPartition(0)
  }
}

watch(() => sessionStore.currentVideoDuration, onVideoUpdate)

onMounted(() => {
  createVideoPartitions()
  fetchEvents()
})
</script>
