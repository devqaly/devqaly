<template>
  <BaseEventDetails
    data-cy="project-session-view__active-event"
    data-event-type="change-url"
  >
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--url-change-source"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">New Url</div>
    <DCopyble :content="props.event.event.toUrl">
      <div
        style="word-break: break-all"
        data-cy="project-session-view__active-event--url-change-url"
        class="font-medium"
        v-text="props.event.event.toUrl"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import BaseEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { UrlChanged } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/resources/events/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<UrlChanged>>()
</script>
