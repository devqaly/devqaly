<template>
  <BaseEvent
    data-cy="list-event__event"
    data-event-type="element-click"
    :session="props.session"
    :event="props.event"
    @see:details="onSeeDetails"
  >
    {{ props.event.event.innerText ?? '&lt;no-inner-text&gt;' }}
  </BaseEvent>
</template>

<script lang="ts" setup>
import type { ElementClick } from '@/services/api/resources/session/events/codec'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import BaseEvent from '@/components/resources/events/listEvent/BaseEvent.vue'
import type { EmitEventsDefinitions } from '@/components/resources/events/listEvent/types/useEventTypes'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

const props = defineProps<{
  event: ElementClick
  session: SessionCodec
}>()

const emit = defineEmits<EmitEventsDefinitions>()

function onSeeDetails(event: EventCodec) {
  emit('see:details', event)
}
</script>
