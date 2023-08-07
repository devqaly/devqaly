import { defineStore } from 'pinia'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { getSession } from '@/services/api/resources/session/actions'
import type { PaginatableRecord } from '@/services/api'
import { emptyPagination } from '@/services/api'
import type {
  DatabaseTransactionEvent,
  ElementClick,
  EventCodec,
  LogEvent,
  NetworkRequestEvent,
  ResizeScreenEvent,
  ScrollEvent,
  UrlChanged
} from '@/services/api/resources/session/events/codec'
import type { GetSessionEventsParameters } from '@/services/api/resources/session/events/requests'
import { getEventsForSession } from '@/services/api/resources/session/events/actions'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import {
  createPartitionsForVideo,
  filterEventsUntilVideoCurrentTime,
  filterLiveEvents,
  findStartAndEndDateForVideoPartition,
  isVideoConverted
} from '@/services/resources/SessionsService'

interface SessionStoreState {
  activeSession: SessionCodec
  activeSessionEventsRequest: PaginatableRecord<EventCodec>
  videoPartitions: Record<number, { hasFetchedEvents: boolean }>
  partitionSize: number
  currentVideoDuration: number
  activeEventDetails: null | EventCodec
  activeNetworkRequest: NetworkRequestEvent | null
}

export const useSessionsStore = defineStore('sessionsStore', {
  state: (): SessionStoreState => ({
    activeSession: sessionsCodecFactory(),
    activeSessionEventsRequest: emptyPagination(),
    videoPartitions: {},
    partitionSize: 10,
    currentVideoDuration: 0,
    activeEventDetails: null,
    activeNetworkRequest: null
  }),
  getters: {
    liveEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterLiveEvents(e, state.activeSession.createdAt, state.currentVideoDuration)
      ),
    networkEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.NETWORK_REQUEST,
          state.activeSession.createdAt
        )
      ) as NetworkRequestEvent[],

    clickEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.ELEMENT_CLICKED,
          state.activeSession.createdAt
        )
      ) as ElementClick[],

    changedUrlEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.CHANGED_URL,
          state.activeSession.createdAt
        )
      ) as UrlChanged[],

    scrollEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.SCROLL,
          state.activeSession.createdAt
        )
      ) as ScrollEvent[],

    resizedScreenEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.RESIZE_SCREEN,
          state.activeSession.createdAt
        )
      ) as ResizeScreenEvent[],

    databaseTransactionEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.DATABASE_TRANSACTION,
          state.activeSession.createdAt
        )
      ) as DatabaseTransactionEvent[],
    logEvents: (state: SessionStoreState) =>
      state.activeSessionEventsRequest.data.filter((e) =>
        filterEventsUntilVideoCurrentTime(
          e,
          state.currentVideoDuration,
          EventTypesEnum.LOG,
          state.activeSession.createdAt
        )
      ) as LogEvent[]
  },
  actions: {
    async getActiveSession(sessionId: string) {
      const { data } = await getSession(sessionId)

      this.activeSession = data.data

      if (isVideoConverted(data.data.videoStatus)) {
        this.videoPartitions = createPartitionsForVideo(
          this.activeSession.videoDurationInSeconds!,
          this.partitionSize
        )
      }
    },

    async getActiveSessionEventsForPartition(videoCurrentTimeInSecond: number) {
      // At `SessionsService::createPartitionsForVideo` we start the range at index 1
      // Therefore we need to take care that the current partition CAN NOT be zero here
      const _currentPartition = Math.floor(videoCurrentTimeInSecond / this.partitionSize)
      const currentPartition = _currentPartition < 1 ? 1 : _currentPartition

      if (this.videoPartitions[currentPartition] === undefined) {
        throw new Error(
          `The partition is not available for this video. Partition: ${currentPartition}`
        )
      }

      let delta: ReturnType<typeof findStartAndEndDateForVideoPartition>
      const leftPartition = currentPartition - 1
      const rightPartition = currentPartition + 1
      const hasFetchedEventsForCurrentPartition =
        this.videoPartitions[currentPartition].hasFetchedEvents
      const hasFetchedEventsForRightPartition =
        this.videoPartitions[rightPartition]?.hasFetchedEvents ?? true
      const hasFetchedEventsForLeftPartition =
        this.videoPartitions[leftPartition]?.hasFetchedEvents ?? true

      // We will be fetching the events for the adjacent partitions to avoid the user having to
      // wait for events to be loaded for the next partitions he visits.

      if (!hasFetchedEventsForCurrentPartition) {
        delta = findStartAndEndDateForVideoPartition(
          currentPartition,
          this.partitionSize,
          this.activeSession.createdAt
        )

        this.videoPartitions[currentPartition].hasFetchedEvents = true

        await this.getActiveSessionEvents(this.activeSession.id, {
          startCreatedAt: delta.startDelta.toISOString(),
          endCreatedAt: delta.endDelta.toISOString()
        }).catch(() => (this.videoPartitions[currentPartition].hasFetchedEvents = false))
      }

      if (!hasFetchedEventsForRightPartition) {
        delta = findStartAndEndDateForVideoPartition(
          rightPartition,
          this.partitionSize,
          this.activeSession.createdAt
        )

        this.videoPartitions[rightPartition].hasFetchedEvents = true

        await this.getActiveSessionEvents(this.activeSession.id, {
          startCreatedAt: delta.startDelta.toISOString(),
          endCreatedAt: delta.endDelta.toISOString()
        }).catch(() => (this.videoPartitions[rightPartition].hasFetchedEvents = false))
      }

      if (leftPartition > -1 && !hasFetchedEventsForLeftPartition) {
        delta = findStartAndEndDateForVideoPartition(
          leftPartition,
          this.partitionSize,
          this.activeSession.createdAt
        )

        this.videoPartitions[leftPartition].hasFetchedEvents = true

        await this.getActiveSessionEvents(this.activeSession.id, {
          startCreatedAt: delta.startDelta.toISOString(),
          endCreatedAt: delta.endDelta.toISOString()
        }).catch(() => (this.videoPartitions[leftPartition].hasFetchedEvents = false))
      }
    },
    async getActiveSessionEvents(sessionId: string, params: GetSessionEventsParameters = {}) {
      const { data } = await getEventsForSession(sessionId, params)

      if (data.data.length > 0) {
        const events = [...data.data, ...this.activeSessionEventsRequest.data]

        this.activeSessionEventsRequest.data = events.sort((a, b) => {
          // @ts-ignore
          return new Date(b.clientUtcEventCreatedAt) - new Date(a.clientUtcEventCreatedAt)
        })
      }

      this.activeSessionEventsRequest.meta = data.meta
      this.activeSessionEventsRequest.links = data.links
    }
  }
})
