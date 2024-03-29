<template>
  <BaseEventDetails
    data-cy="project-session-view__active-event"
    data-event-type="database-transaction"
  >
    <div class="text-gray-500">Source</div>

    <DCopyble :content="props.event.source">
      <div
        data-cy="project-session-view__active-event--db-source"
        class="font-medium"
        v-text="props.event.source"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">Execution time (milliseconds)</div>

    <DCopyble :content="props.event.event.executionTimeInMilliseconds ?? 'no-execution-time'">
      <div
        class="font-medium"
        data-cy="project-session-view__active-event--db-execution"
        v-text="props.event.event.executionTimeInMilliseconds"
      />
    </DCopyble>

    <div class="text-gray-500 mt-2">SQL</div>
    <DCopyble :content="props.event.event.sql">
      <div
        class="font-medium max-w-full max-h-[5rem] overflow-auto"
        data-cy="project-session-view__active-event--db-sql"
        style="word-break: break-all"
        v-text="props.event.event.sql"
      />
    </DCopyble>

    <template v-if="networkEvent">
      <h2 class="text-2xl mt-4 mb-2">Network Request</h2>

      <div class="text-gray-500 mt-2">Request ID</div>
      <DCopyble :content="networkEvent.event.requestId ?? 'no request id'">
        <div v-if="networkEvent.event.requestId === null">no request id</div>
        <div
          class="font-medium underline cursor-pointer"
          style="word-break: break-all"
          v-else
          @click="onClickRequestId"
          v-text="props.event.event.requestId"
        />
      </DCopyble>

      <div class="text-gray-500 mt-2">URL</div>
      <DCopyble :content="networkEvent.event.url">
        <div
          class="font-medium"
          style="word-break: break-all"
          v-text="networkEvent.event.url"
        />
      </DCopyble>

      <div class="text-gray-500 mt-2">Method</div>
      <DCopyble :content="networkEvent.event.method">
        <div
          class="font-medium"
          v-text="networkEvent.event.method"
        />
      </DCopyble>

      <div class="text-gray-500 mt-2">Response Code</div>
      <DCopyble :content="networkEvent.event.responseStatus ?? 'no-response'">
        {{ networkEvent.event.responseStatus ?? 'no-response' }}
      </DCopyble>

      <div class="text-gray-500 mt-2">Request Headers</div>
      <DCopyble :content="fromHeaderToText(networkEvent.event.requestHeaders)">
        <div v-if="networkEvent.event.requestHeaders === null">No Headers</div>

        <div
          class="max-w-full max-h-[5rem] overflow-auto"
          v-else
        >
          <div
            :key="headerKey"
            v-for="headerKey in Object.keys(networkEvent.event.requestHeaders)"
            style="word-break: break-all"
          >
            {{ headerKey }}: {{ networkEvent.event.requestHeaders[headerKey] }}
          </div>
        </div>
      </DCopyble>

      <div class="text-gray-500 mt-2">Request Body</div>
      <DCopyble :content="networkEvent.event.requestBody ?? '<no-body>'">
        <div
          class="font-medium max-w-full max-h-[5rem] overflow-auto"
          style="word-break: break-all"
          v-text="networkEvent.event.requestBody ?? '<no-body>'"
        />
      </DCopyble>

      <div class="text-gray-500 mt-2">Response Headers</div>
      <DCopyble :content="fromHeaderToText(networkEvent.event.responseHeaders)">
        <div v-if="networkEvent.event.responseHeaders === null">No Headers</div>

        <div
          class="max-w-full max-h-[5rem] overflow-auto"
          v-else
        >
          <div
            :key="headerKey"
            v-for="headerKey in Object.keys(networkEvent.event.responseHeaders)"
          >
            {{ headerKey }}: {{ networkEvent.event.responseHeaders[headerKey] }}
          </div>
        </div>
      </DCopyble>

      <div class="text-gray-500 mt-2">Response Body</div>
      <DCopyble :content="networkEvent.event.responseBody ?? '<no-body>'">
        <div
          class="font-medium max-w-full max-h-[5rem] overflow-auto"
          style="overflow-wrap: anywhere"
          v-text="networkEvent.event.responseBody ?? '<no-body>'"
        />
      </DCopyble>
    </template>
  </BaseEventDetails>
</template>

<script setup lang="ts">
import type {
  DatabaseTransactionEvent,
  NetworkRequestEvent
} from '@/services/api/resources/session/events/codec'
import BaseEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/BaseEventDetails.vue'
import type { Props } from '@/components/resources/events/EventTabs/ActiveEvent/common'
import DCopyble from '@/components/DCopyble.vue'
import { computed } from 'vue'
import { useSessionsStore } from '@/stores/sessions'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import { fromHeaderToText } from '@/services/resources/events/NetworkRequestEventService'

const props = defineProps<Props<DatabaseTransactionEvent>>()

const sessionStore = useSessionsStore()

const networkEvent = computed<NetworkRequestEvent | null>(() => {
  if (props.event.event.requestId === null) return null

  const networkEvent = sessionStore.activeSessionEventsRequest.data.find((e) => {
    if (e.type !== EventTypesEnum.NETWORK_REQUEST) return false

    return (e as NetworkRequestEvent).event.requestId === props.event.event.requestId
  })

  return (networkEvent as NetworkRequestEvent | undefined) ?? null
})

function onClickRequestId() {
  if (networkEvent.value === null) {
    throw new Error('`networkEvent` should not be null here')
  }

  sessionStore.activeNetworkRequest = networkEvent.value
}
</script>
