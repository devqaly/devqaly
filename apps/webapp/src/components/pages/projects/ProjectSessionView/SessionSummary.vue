<template>
  <section
    class="flex align-items-start flex-column border-round-md border-1 border-gray-300 surface-ground p-3 h-full"
  >
    <div>
      <div class="text-gray-500">OS</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="sessionStore.activeSession.os"
      />
      <Skeleton
        width="60px"
        height="17px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Platform</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="sessionStore.activeSession.platformName"
      />
      <Skeleton
        height="17px"
        width="60px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Version</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="sessionStore.activeSession.version"
      />
      <Skeleton
        height="17px"
        width="60px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Screen (px)</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="
          `${sessionStore.activeSession.windowWidth}x${sessionStore.activeSession.windowHeight}`
        "
      />

      <Skeleton
        width="60px"
        height="17px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Created By</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="sessionStore.activeSession.createdBy?.fullName"
      />
      <Skeleton
        height="17px"
        width="60px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Video Status</div>
      <div
        class="font-medium"
        v-text="videoStatusConversion"
        v-if="!fetchingSession"
      />
      <Skeleton
        height="17px"
        width="60px"
        v-if="fetchingSession"
      />
    </div>

    <div class="mt-3">
      <div class="text-gray-500">Created At</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        v-text="formatToDateTime(sessionStore.activeSession.createdAt)"
      />
      <Skeleton
        height="17px"
        width="60px"
        v-if="fetchingSession"
      />
    </div>
  </section>
</template>

<script lang="ts" setup>
import { useSessionsStore } from '@/stores/sessions'
import { computed } from 'vue'
import { getVideoStatusText } from '@/services/resources/SessionsService'
import { formatToDateTime } from '@/services/date'

const sessionStore = useSessionsStore()

defineProps({
  fetchingSession: { type: Boolean }
})

const videoStatusConversion = computed(() =>
  getVideoStatusText(sessionStore.activeSession.videoStatus)
)
</script>
