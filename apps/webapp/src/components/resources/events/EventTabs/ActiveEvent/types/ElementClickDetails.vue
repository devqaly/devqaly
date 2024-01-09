<template>
  <BaseEventDetails
    data-cy="project-session-view__active-event"
    data-event-type="element-click"
  >
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        data-cy="project-session-view__active-event--click-source"
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Position X</div>
    <DCopyble :content="props.event.event.positionX">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--click-position-x"
        v-text="props.event.event.positionX"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Position Y</div>
    <DCopyble :content="props.event.event.positionY">
      <div
        data-cy="project-session-view__active-event--click-position-y"
        class="font-medium"
        v-text="props.event.event.positionY"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Element Classes</div>
    <DCopyble :content="props.event.event.elementClasses ?? 'no-classes'">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--click-classes"
        v-text="props.event.event.elementClasses"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Inner Text</div>
    <DCopyble :content="props.event.event.innerText ?? 'no-inner-text'">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--click-inner-text"
        v-text="props.event.event.innerText"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import BaseEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { ElementClick } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/resources/events/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<ElementClick>>()
</script>
