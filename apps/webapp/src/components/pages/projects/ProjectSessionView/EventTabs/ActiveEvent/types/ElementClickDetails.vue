<template>
  <BaseEventDetails>
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Position X</div>
    <DCopyble :content="props.event.event.positionX">
      <div
        class="font-medium"
        v-text="props.event.event.positionX"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Position Y</div>
    <DCopyble :content="props.event.event.positionY">
      <div
        class="font-medium"
        v-text="props.event.event.positionY"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Element Classes</div>
    <DCopyble :content="props.event.event.elementClasses ?? 'no-classes'">
      <div
        class="font-medium"
        v-text="props.event.event.elementClasses"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Inner Text</div>
    <DCopyble :content="props.event.event.innerText ?? 'no-inner-text'">
      <div
        class="font-medium"
        v-text="props.event.event.innerText"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import BaseEventDetails from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { ElementClick } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<ElementClick>>()
</script>
