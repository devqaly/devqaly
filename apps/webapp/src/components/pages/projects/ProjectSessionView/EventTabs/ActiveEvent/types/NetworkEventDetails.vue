<template>
  <BaseEventDetails>
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">URL</div>
    <DCopyble :content="props.event.event.url">
      <div
        class="font-medium"
        style="word-break: break-all"
        v-text="props.event.event.url"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Request ID</div>
    <DCopyble :content="props.event.event.requestId ?? 'no request id'">
      <div v-if="props.event.event.requestId === null">no request id</div>
      <div
        class="font-medium underline cursor-pointer w-fit"
        style="word-break: break-all"
        v-else
        v-text="props.event.event.requestId"
        @click="onClickRequestId"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Method</div>
    <DCopyble :content="props.event.event.method">
      <div
        class="font-medium"
        v-text="props.event.event.method"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Response Code</div>
    <DCopyble :content="props.event.event.responseStatus ?? 'no-response'">
      {{ props.event.event.responseStatus ?? 'no-response' }}
    </DCopyble>

    <div class="text-gray-500 mt-2">Request Headers</div>
    <DCopyble :content="fromHeaderToText(props.event.event.requestHeaders)">
      <div v-if="props.event.event.requestHeaders === null">No Headers</div>

      <div
        class="max-w-full max-h-5rem overflow-auto"
        v-else
      >
        <div
          :key="headerKey"
          v-for="headerKey in Object.keys(props.event.event.requestHeaders)"
          style="word-break: break-all"
        >
          {{ headerKey }}: {{ props.event.event.requestHeaders[headerKey] }}
        </div>
      </div>
    </DCopyble>

    <div class="text-gray-500 mt-2">Request Body</div>
    <DCopyble :content="props.event.event.requestBody ?? '<no-body>'">
      <div
        class="font-medium max-w-full max-h-5rem overflow-auto"
        style="word-break: break-all"
        v-text="props.event.event.requestBody ?? '<no-body>'"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Response Headers</div>
    <DCopyble :content="fromHeaderToText(props.event.event.responseHeaders)">
      <div v-if="props.event.event.responseHeaders === null">No Headers</div>

      <div
        class="max-w-full max-h-5rem overflow-auto"
        v-else
      >
        <div
          :key="headerKey"
          v-for="headerKey in Object.keys(props.event.event.responseHeaders)"
          style="word-break: break-all"
        >
          {{ headerKey }}: {{ props.event.event.responseHeaders[headerKey] }}
        </div>
      </div>
    </DCopyble>

    <div class="text-gray-500 mt-2">Response Body</div>
    <DCopyble :content="props.event.event.responseBody ?? '<no-body>'">
      <div
        class="font-medium max-w-full max-h-5rem overflow-auto"
        style="word-break: break-all"
        v-text="props.event.event.responseBody ?? '<no-body>'"
      />
    </DCopyble>
  </BaseEventDetails>
</template>

<script lang="ts" setup>
import type { NetworkRequestEvent } from '@/services/api/resources/session/events/codec'
import BaseEventDetails from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { Props } from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'
import { fromHeaderToText } from '@/services/resources/events/NetworkRequestEventService'
import { useSessionsStore } from '@/stores/sessions'

const props = defineProps<Props<NetworkRequestEvent>>()

const sessionStore = useSessionsStore()

function onClickRequestId() {
  sessionStore.activeNetworkRequest = props.event
}
</script>
