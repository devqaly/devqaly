<template>
  <BaseEventDetails
    data-cy="project-session-view__active-event"
    data-event-type="scroll"
  >
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        data-cy="project-session-view__active-event--scroll-source"
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">
      Scroll Height
      <i
        class="pi pi-info-circle"
        v-tooltip="'The total vertical area'"
      />
    </div>

    <DCopyble :content="props.event.event.scrollHeight">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--scroll-height"
        v-text="props.event.event.scrollHeight"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">
      Scroll Width
      <i
        class="pi pi-info-circle"
        v-tooltip="'The total horizontal area'"
      />
    </div>

    <DCopyble :content="props.event.event.scrollWidth">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--scroll-width"
        v-text="props.event.event.scrollWidth"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">
      Scroll Top
      <i
        class="pi pi-info-circle"
        v-tooltip="'How many pixels scrolled away from the top'"
      />
    </div>

    <DCopyble :content="props.event.event.scrollTop">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--scroll-top"
        v-text="props.event.event.scrollTop"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">
      Scroll Left

      <i
        class="pi pi-info-circle"
        v-tooltip="'How many pixels scrolled away from the left'"
      />
    </div>

    <DCopyble :content="props.event.event.scrollLeft">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--scroll-left"
        v-text="props.event.event.scrollLeft"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script lang="ts" setup>
import BaseEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { ScrollEvent } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/resources/events/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<ScrollEvent>>()
</script>
