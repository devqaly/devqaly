<template>
  <div class="video-container">
    <div class="video-container rounded-lg overflow-hidden">
      <video
        data-cy="project-session-view__video"
        ref="videoNode"
        class="w-full rounded-lg"
        controls
        preload="auto"
        :src="session.videoUrl as string"
        width="100%"
        @timeupdate="onTimeUpdate"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onUnmounted, PropType, ref } from 'vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { eventBus, EventBusEvents } from '@/services/mitt'
import { differenceInSeconds } from 'date-fns'
import type { SessionCodec } from '@/services/api/resources/session/codec'

const props = defineProps({
  session: { type: Object as PropType<SessionCodec>, required: true }
})

const emit = defineEmits<{
  'update:videoTime': [event: HTMLVideoElement]
}>()

const videoNode = ref<HTMLVideoElement | null>(null)

eventBus.on(EventBusEvents.REQUEST_CLICKED, onEventClicked)

function onEventClicked(event: EventCodec) {
  if (videoNode.value === null || !props.session) return

  const eventHappenedAt = new Date(event.clientUtcEventCreatedAt)
  const videoStartedAt = new Date(props.session.createdAt)

  ;(videoNode.value as HTMLVideoElement).currentTime =
    differenceInSeconds(eventHappenedAt, videoStartedAt, {
      roundingMethod: 'floor'
    }) - 1
}

function onTimeUpdate(e: Event) {
  // const target = e.target as HTMLVideoElement
  //
  // sessionStore.currentVideoDuration = target.currentTime
  emit('update:videoTime', e.target as HTMLVideoElement)
}

onUnmounted(() => {
  eventBus.off(EventBusEvents.REQUEST_CLICKED, onEventClicked)
})
</script>

<style>
.video-container {
  width: 100%;
}
</style>
