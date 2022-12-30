<template>
  <BaseEventDetails
    data-cy="project-session-view__active-event"
    data-event-type="resize-screen"
  >
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--resize-source"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Inner Width</div>

    <DCopyble :content="props.event.event.innerWidth">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--resize-inner-width"
        v-text="props.event.event.innerWidth"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Inner Height</div>

    <DCopyble :content="props.event.event.innerHeight">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--resize-inner-height"
        v-text="props.event.event.innerHeight"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import BaseEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { ResizeScreenEvent } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/resources/events/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<ResizeScreenEvent>>()
</script>
