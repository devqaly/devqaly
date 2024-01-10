<template>
  <div>
    <div
      v-for="request in sessionStore.clickEvents"
      :key="request.id"
    >
      <ElementClick
        :session="sessionStore.activeSession"
        :event="request"
        @see:details="onSeeMoreDetails"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useSessionsStore } from '@/stores/sessions'
import ElementClick from '@/components/resources/events/listEvent/types/ElementClick.vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

const sessionStore = useSessionsStore()

function onSeeMoreDetails(event: EventCodec) {
  sessionStore.activeEventDetails = event
}
</script>
