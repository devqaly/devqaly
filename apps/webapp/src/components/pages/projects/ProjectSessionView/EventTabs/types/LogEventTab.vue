<template>
  <div>
    <div
      v-for="request in sessionStore.logEvents"
      :key="request.id"
    >
      <LogEvent
        :session="sessionStore.activeSession"
        :event="request"
        @see:details="onSeeMoreDetails"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useSessionsStore } from '@/stores/sessions'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import LogEvent from '@/components/resources/events/listEvent/types/LogEvent.vue'

const sessionStore = useSessionsStore()

function onSeeMoreDetails(event: EventCodec) {
  sessionStore.activeEventDetails = event
}
</script>
