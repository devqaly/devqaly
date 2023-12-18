<template>
  <BaseEvent
    data-cy="list-event__event"
    data-event-type="network-request"
    :session="props.session"
    :event="props.event"
    @see:details="onSeeDetails"
  >
    <Badge
      :value="event.event.method"
      :pt="{ root: { class: 'rounded-lg' } }"
    />
    {{ event.event.url }}
  </BaseEvent>
</template>

<script lang="ts" setup>
import type { EventCodec, NetworkRequestEvent } from '@/services/api/resources/session/events/codec'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import BaseEvent from '@/components/resources/events/listEvent/BaseEvent.vue'
import type { EmitEventsDefinitions } from '@/components/resources/events/listEvent/types/useEventTypes'

const props = defineProps<{
  event: NetworkRequestEvent
  session: SessionCodec
}>()

const emit = defineEmits<EmitEventsDefinitions>()

function onSeeDetails(event: EventCodec) {
  emit('see:details', event)
}
</script>
