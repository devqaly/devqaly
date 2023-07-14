<template>
  <BaseEvent
    :session="props.session"
    :event="props.event"
    @see:details="onSeeDetails"
  >
    {{
      props.event.event.sql.length > 150
        ? `${props.event.event.sql.slice(0, 150)}...`
        : props.event.event.sql
    }}
  </BaseEvent>
</template>

<script lang="ts" setup>
import type { DatabaseTransactionEvent } from '@/services/api/resources/session/events/codec'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import BaseEvent from '@/components/resources/events/listEvent/BaseEvent.vue'
import type { EmitEventsDefinitions } from '@/components/resources/events/listEvent/types/useEventTypes'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

const props = defineProps<{
  event: DatabaseTransactionEvent
  session: SessionCodec
}>()

const emit = defineEmits<EmitEventsDefinitions>()

function onSeeDetails(event: EventCodec) {
  emit('see:details', event)
}
</script>
