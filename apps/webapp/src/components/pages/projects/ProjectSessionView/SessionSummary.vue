<template>
  <section
    class="flex items-start flex-col rounded-lg border border-gray-300 bg-slate-100 p-3 h-full"
  >
    <div>
      <div class="text-gray-500">OS</div>
      <div
        class="font-medium"
        v-if="!fetchingSession"
        data-cy="project-session-view__os"
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
        data-cy="project-session-view__platform"
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
      <div class="text-gray-500">Environment</div>
      <div
        class="font-medium"
        data-cy="project-session-view__environment"
        v-if="!fetchingSession"
        v-text="sessionStore.activeSession.environment ?? '---'"
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
        data-cy="project-session-view__version"
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
        data-cy="project-session-view__screen-size"
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
        data-cy="project-session-view__created-by"
        v-text="sessionStore.activeSession.createdBy?.fullName ?? 'Unassigned'"
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
        data-cy="project-session-view__video-status"
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
        data-cy="project-session-view__created-at"
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
