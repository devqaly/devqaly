import type { DateTime, ResourceID, UUID } from '@/services/api'
import type { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import { LOG_LEVEL } from '@/services/api/resources/session/events/constants'

export interface BaseEventCodec<T> {
  id: ResourceID
  createdAt: DateTime
  clientUtcEventCreatedAt: DateTime
  source: string
  type: EventTypesEnum
  event: T
}

export interface BaseNetworkRequestEvent {
  id: ResourceID
  method: 'GET' | 'POST' | 'PUT' | string
  requestBody: string | null
  requestHeaders: Record<string, string> | null
  responseBody: string | null
  responseHeaders: Record<string, string> | null
  responseStatus: string | null
  url: string
  requestId: UUID | null
}

export interface BaseChangedUrlEvent {
  id: ResourceID
  toUrl: string
}

export interface BaseElementClick {
  id: ResourceID
  positionX: number
  positionY: number
  elementClasses: string | null
  innerText: string | null
}

export interface BaseScroll {
  id: ResourceID
  scrollHeight: number
  scrollLeft: number
  scrollTop: number
  scrollWidth: number
}

export interface BaseResizeScreen {
  id: ResourceID
  innerHeight: number
  innerWidth: number
}

export interface BaseDatabaseTransaction {
  id: ResourceID
  sql: string
  executionTimeInMilliseconds: number | null
  requestId: UUID | null
}

export interface BaseLogEvent {
  id: ResourceID
  log: string
  level: LOG_LEVEL
  requestId: UUID | null
}

export type NetworkRequestEvent = BaseEventCodec<BaseNetworkRequestEvent>
export type UrlChanged = BaseEventCodec<BaseChangedUrlEvent>
export type ElementClick = BaseEventCodec<BaseElementClick>
export type ScrollEvent = BaseEventCodec<BaseScroll>
export type ResizeScreenEvent = BaseEventCodec<BaseResizeScreen>
export type DatabaseTransactionEvent = BaseEventCodec<BaseDatabaseTransaction>
export type LogEvent = BaseEventCodec<BaseLogEvent>

export type EventCodec =
  | NetworkRequestEvent
  | UrlChanged
  | ElementClick
  | ScrollEvent
  | ResizeScreenEvent
  | DatabaseTransactionEvent
  | LogEvent
