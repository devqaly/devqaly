<template>
  <Sidebar
    class="w-full md:w-20rem lg:w-30rem"
    v-model:visible="isNetworkRequestSidebarOpen"
  >
    <template v-if="sessionStore.activeNetworkRequest">
      <h2
        v-text="sessionStore.activeNetworkRequest.event.url"
        style="word-break: break-all"
      />

      <h4>Database Transactions ({{ databaseTransactionsRequest.meta.total }})</h4>

      <template v-if="isLoadingDatabaseTransactions">
        <Skeleton height="60px" />
        <Skeleton
          height="60px"
          class="mt-1"
        />
      </template>

      <DCopyble
        v-for="event in databaseTransactionsRequest.data"
        :content="event.event.sql"
        :key="`active-network-request-db-${event.id}`"
        class="p-2 border-bottom-1 border-gray-300"
      >
        <div class="max-h-4rem overflow-y-auto">{{ event.event.sql }}</div>
      </DCopyble>

      <Button
        class="mt-2"
        v-if="hasNextPage(databaseTransactionsRequest.links)"
        outlined
        :loading="isLoadingMoreDatabaseTransactions"
        @click="onLoadMoreDatabaseTransactions"
        >Load more</Button
      >

      <h4>Logs ({{ logsRequest.meta.total }})</h4>

      <template v-if="isLoadingLogs">
        <Skeleton height="60px" />
        <Skeleton
          height="60px"
          class="mt-1"
        />
      </template>

      <DCopyble
        v-for="event in logsRequest.data"
        :content="event.event.log"
        :key="`active-log-request-db-${event.id}`"
        class="p-2 border-bottom-1 border-gray-300"
      >
        <div class="max-h-4rem overflow-y-auto">{{ event.event.log }}</div>
      </DCopyble>

      <Button
        class="mt-2"
        v-if="hasNextPage(logsRequest.links)"
        outlined
        :loading="isLoadingMoreLogs"
        @click="onLoadMoreLogs"
        >Load more</Button
      >
    </template>
  </Sidebar>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useSessionsStore } from '@/stores/sessions'
import DCopyble from '@/components/DCopyble.vue'
import { emptyPagination } from '@/services/api'
import type {
  DatabaseTransactionEvent,
  LogEvent
} from '@/services/api/resources/session/events/codec'
import {
  getDatabaseTransactionsForRequestId,
  getLogsForRequestId
} from '@/services/api/resources/session/events/actions'
import { hasNextPage } from '@/services/ui'

const sessionStore = useSessionsStore()

const isLoadingDatabaseTransactions = ref(false)

const isLoadingLogs = ref(false)

const isLoadingMoreDatabaseTransactions = ref(false)

const isLoadingMoreLogs = ref(false)

const databaseTransactionsRequest = ref(emptyPagination<DatabaseTransactionEvent>())

const logsRequest = ref(emptyPagination<LogEvent>())

const isNetworkRequestSidebarOpen = computed({
  get() {
    return sessionStore.activeNetworkRequest !== null
  },
  set(value: boolean) {
    if (!value) {
      sessionStore.activeNetworkRequest = null
    }
  }
})

async function onLoadMoreDatabaseTransactions() {
  isLoadingMoreDatabaseTransactions.value = true
  await fetchDatabaseTransactions(databaseTransactionsRequest.value.meta.currentPage + 1)
  isLoadingMoreDatabaseTransactions.value = false
}

async function onLoadMoreLogs() {
  isLoadingMoreLogs.value = true
  await fetchLogs(logsRequest.value.meta.currentPage + 1)
  isLoadingMoreLogs.value = false
}

async function fetchDatabaseTransactions(page = 1) {
  if (
    sessionStore.activeNetworkRequest === null ||
    sessionStore.activeNetworkRequest.event.requestId === null
  ) {
    throw new Error('`sessionStore.activeNetworkRequest` should be defined at this point')
  }

  const response = await getDatabaseTransactionsForRequestId(
    sessionStore.activeNetworkRequest.event.requestId,
    { perPage: 50, page }
  )

  databaseTransactionsRequest.value.data = [
    ...databaseTransactionsRequest.value.data,
    ...response.data.data
  ]
  databaseTransactionsRequest.value.meta = response.data.meta
  databaseTransactionsRequest.value.links = response.data.links
}

async function fetchLogs(page = 1) {
  if (
    sessionStore.activeNetworkRequest === null ||
    sessionStore.activeNetworkRequest.event.requestId === null
  ) {
    throw new Error('`sessionStore.activeNetworkRequest` should be defined at this point')
  }

  const response = await getLogsForRequestId(sessionStore.activeNetworkRequest.event.requestId, {
    perPage: 50,
    page
  })

  logsRequest.value.data = [...logsRequest.value.data, ...response.data.data]
  logsRequest.value.meta = response.data.meta
  logsRequest.value.links = response.data.links
}

watch(
  () => sessionStore.activeNetworkRequest,
  async (request) => {
    if (request === null) return

    isLoadingDatabaseTransactions.value = true
    isLoadingLogs.value = true

    databaseTransactionsRequest.value = emptyPagination()
    logsRequest.value = emptyPagination()

    await Promise.all([fetchDatabaseTransactions(1), fetchLogs(1)])

    isLoadingDatabaseTransactions.value = false
    isLoadingLogs.value = false
  }
)
</script>
