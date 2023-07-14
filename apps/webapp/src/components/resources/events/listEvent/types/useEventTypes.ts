import type { SessionCodec } from '@/services/api/resources/session/codec'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { ref } from 'vue'
import { computed } from 'vue'
import { intervalToDuration } from 'date-fns'
import { eventBus, EventBusEvents } from '@/services/mitt'

export type EmitEventsDefinitions = {
  'see:details': [event: EventCodec]
}

export function useEventTypes<T extends EventCodec>(
  session: SessionCodec,
  event: T,
  emit: ReturnType<typeof defineEmits>
) {
  const _session = ref(session)
  const _event = ref(event)

  const zeroPad = (num: number) => String(num).padStart(2, '0')

  const videoTimeStamp = computed(() => {
    // We will be assuming that the video starts as soon as the session starts
    const videoStart = new Date(_session.value.createdAt)
    const eventHappenedAt = new Date(_event.value.createdAt)

    const duration = intervalToDuration({ start: videoStart, end: eventHappenedAt })

    return `${zeroPad(duration.minutes!)}:${zeroPad(duration.seconds!)}`
  })

  function onEventClick() {
    eventBus.emit(EventBusEvents.REQUEST_CLICKED, _event.value)
  }

  function onSeeMoreDetailsClick() {
    emit('see:details', event)
  }

  return {
    videoTimeStamp,
    onEventClick,
    onSeeMoreDetailsClick
  }
}
