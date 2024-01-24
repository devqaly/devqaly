import type {
  BaseEventCodec,
  CustomEvent,
  DatabaseTransactionEvent,
  ElementClick,
  LogEvent,
  NetworkRequestEvent,
  ResizeScreenEvent,
  ScrollEvent,
  UrlChanged
} from '@/services/api/resources/session/events/codec'
import { EventTypesEnum, LOG_LEVEL } from '@/services/api/resources/session/events/constants'

const baseRequestFactory = function <T = any>(
  type: BaseEventCodec<T>['type']
): Omit<BaseEventCodec<T>, 'event'> {
  return {
    id: '',
    createdAt: '',
    clientUtcEventCreatedAt: '',
    source: '',
    type
  }
}

export const networkRequestEventCodecFactory = (): NetworkRequestEvent => ({
  ...baseRequestFactory(EventTypesEnum.NETWORK_REQUEST),
  event: {
    id: '',
    method: 'GET',
    requestBody: null,
    requestHeaders: null,
    responseBody: null,
    responseHeaders: null,
    responseStatus: null,
    url: '',
    requestId: null
  }
})

export const changedUrlEventCodecFactory = (): UrlChanged => ({
  ...baseRequestFactory(EventTypesEnum.CHANGED_URL),
  event: {
    id: '',
    toUrl: ''
  }
})

export const elementClickEventCodecFactory = (): ElementClick => ({
  ...baseRequestFactory(EventTypesEnum.ELEMENT_CLICKED),
  event: {
    id: '',
    positionX: 0,
    positionY: 0,
    elementClasses: null,
    innerText: null
  }
})

export const scrollEventCodecFactory = (): ScrollEvent => ({
  ...baseRequestFactory(EventTypesEnum.SCROLL),
  event: {
    id: '',
    scrollHeight: 0,
    scrollLeft: 0,
    scrollTop: 0,
    scrollWidth: 0
  }
})

export const resizeScreenEventCodecFactory = (): ResizeScreenEvent => ({
  ...baseRequestFactory(EventTypesEnum.RESIZE_SCREEN),
  event: {
    id: '',
    innerHeight: 0,
    innerWidth: 0
  }
})

export const databaseTransactionEventCodecFactory = (): DatabaseTransactionEvent => ({
  ...baseRequestFactory(EventTypesEnum.DATABASE_TRANSACTION),
  event: {
    id: '',
    sql: '',
    executionTimeInMilliseconds: null,
    requestId: null
  }
})

export const logEventCodecFactory = (): LogEvent => ({
  ...baseRequestFactory(EventTypesEnum.LOG),
  event: {
    id: '',
    log: '',
    level: LOG_LEVEL.ERROR,
    requestId: null
  }
})

export const customEventCodecFactory = (): CustomEvent => ({
  ...baseRequestFactory(EventTypesEnum.CUSTOM_EVENT),
  event: {
    id: '',
    name: '',
    payload: {}
  }
})
