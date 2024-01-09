<template>
  <section
    class="flex items-start flex-col rounded-lg border border-gray-300 bg-slate-100 p-3 h-full"
  >
    <div>
      <div class="text-gray-500">OS</div>
      <div
        class="font-medium"
        v-if="!fetchingSession && session"
        data-cy="project-session-view__os"
        v-text="session.os"
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
        v-if="!fetchingSession && session"
        v-text="session.platformName"
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
        v-text="session ? session.environment ?? '---' : '---'"
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
        v-if="!fetchingSession && session"
        v-text="session.version"
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
        v-if="!fetchingSession && session"
        v-text="`${session.windowWidth}x${session.windowHeight}`"
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
        v-text="session ? session.createdBy?.fullName ?? 'Unassigned' : 'Unassigned'"
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
        v-if="!fetchingSession && session"
        v-text="formatToDateTime(session.createdAt)"
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
import { computed, PropType } from 'vue'
import { getVideoStatusText } from '@/services/resources/SessionsService'
import { formatToDateTime } from '@/services/date'
import type { SessionCodec } from '@/services/api/resources/session/codec'

const props = defineProps({
  fetchingSession: { type: Boolean },
  session: { type: Object as PropType<SessionCodec | null>, required: true }
})

const videoStatusConversion = computed(() =>
  props.session ? getVideoStatusText(props.session.videoStatus) : '...'
)
</script>
