<template>
  <BaseEventDetails>
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">New Url</div>
    <DCopyble :content="props.event.event.toUrl">
      <div
        style="word-break: break-all"
        class="font-medium"
        v-text="props.event.event.toUrl"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import BaseEventDetails from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { UrlChanged } from '@/services/api/resources/session/events/codec'
import type { Props } from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'

const props = defineProps<Props<UrlChanged>>()
</script>
