<template>
  <div
    class="grid border-bottom-1 border-gray-300 max-w-full align-items-center px-2 py-1"
    style="margin: 0"
    :data-event-id="event.id"
  >
    <div
      class="col-fixed cursor-pointer p-0"
      style="width: fit-content"
      @click="onEventClick"
    >
      {{ videoTimeStamp }}
    </div>

    <div
      class="col"
      style="word-break: break-all"
    >
      <slot name="default" />
    </div>

    <div
      class="col-fixed p-0 align-self-center"
      style="width: fit-content"
    >
      <Button
        data-cy="list-event__open-details"
        :data-event-id="event.id"
        class="z-0"
        icon="pi pi-chevron-right"
        severity="primary"
        text
        rounded
        @click="onSeeMoreDetailsClick"
        :pt="{ root: { style: 'height: 20px; width: 20px; padding: 15px' } }"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { EmitEventsDefinitions } from '@/components/resources/events/listEvent/types/useEventTypes'
import { useEventTypes } from '@/components/resources/events/listEvent/types/useEventTypes'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import type { SessionCodec } from '@/services/api/resources/session/codec'

const props = defineProps<{
  event: EventCodec
  session: SessionCodec
}>()

const emit = defineEmits<EmitEventsDefinitions>()

const { onEventClick, videoTimeStamp, onSeeMoreDetailsClick } = useEventTypes<EventCodec>(
  props.session,
  props.event,
  emit
)
</script>
