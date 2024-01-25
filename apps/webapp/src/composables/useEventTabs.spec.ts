import { beforeEach, describe, expect, it } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type {
  CustomEvent,
  DatabaseTransactionEvent,
  ElementClick,
  EventCodec,
  LogEvent,
  NetworkRequestEvent,
  ResizeScreenEvent,
  ScrollEvent,
  UrlChanged
} from '@/services/api/resources/session/events/codec'
import { randomInteger, range } from '@/services/number'
import {
  changedUrlEventCodecFactory,
  customEventCodecFactory,
  databaseTransactionEventCodecFactory,
  elementClickEventCodecFactory,
  logEventCodecFactory,
  networkRequestEventCodecFactory,
  resizeScreenEventCodecFactory,
  scrollEventCodecFactory
} from '@/services/factories/eventsFactory'
import type { SessionStoreState } from '@/stores/sessions'
import { useSessionsStore } from '@/stores/sessions'
import { emptyPagination } from '@/services/api'
import { useEventTabs } from '@/composables/useEventTabs'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { sessionsCodecFactory } from '@/services/factories/sessionsFactory'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'

function setDates<T extends EventCodec>(event: T) {
  return {
    ...event,
    clientUtcEventCreatedAt: new Date().toUTCString()
  }
}

describe('useEventTabs.ts', () => {
  let networkEvents: NetworkRequestEvent[],
    changedUrlEvents: UrlChanged[],
    elementClickEvents: ElementClick[],
    scrollEvents: ScrollEvent[],
    resizeScreenEvents: ResizeScreenEvent[],
    databaseTransactionEvents: DatabaseTransactionEvent[],
    logEvents: LogEvent[],
    customEvents: CustomEvent[],
    events: EventCodec[],
    requestEvent: SessionStoreState['activeSessionEventsRequest'],
    activeSession: SessionCodec

  beforeEach(() => {
    setActivePinia(createPinia())

    networkEvents = range(0, randomInteger(2, 10)).map(() =>
      setDates(networkRequestEventCodecFactory())
    )
    changedUrlEvents = range(0, randomInteger(2, 10)).map(() =>
      setDates(changedUrlEventCodecFactory())
    )
    elementClickEvents = range(0, randomInteger(2, 10)).map(() =>
      setDates(elementClickEventCodecFactory())
    )
    scrollEvents = range(0, randomInteger(2, 10)).map(() => setDates(scrollEventCodecFactory()))
    resizeScreenEvents = range(0, randomInteger(2, 10)).map(() =>
      setDates(resizeScreenEventCodecFactory())
    )
    databaseTransactionEvents = range(0, randomInteger(2, 10)).map(() =>
      setDates(databaseTransactionEventCodecFactory())
    )
    logEvents = range(0, randomInteger(2, 10)).map(() => setDates(logEventCodecFactory()))
    customEvents = range(0, randomInteger(2, 10)).map(() => setDates(customEventCodecFactory()))

    events = [
      ...networkEvents,
      ...changedUrlEvents,
      ...elementClickEvents,
      ...scrollEvents,
      ...resizeScreenEvents,
      ...databaseTransactionEvents,
      ...logEvents,
      ...customEvents
    ]

    requestEvent = {
      ...emptyPagination(),
      data: events
    }

    activeSession = {
      ...sessionsCodecFactory(),
      createdAt: new Date().toUTCString()
    }
  })

  it('should return correct number of events per event type', async () => {
    const sessionStore = useSessionsStore()

    sessionStore.activeSessionEventsRequest = requestEvent
    sessionStore.activeSession = activeSession

    const { tabs } = useEventTabs()

    expect(tabs.value.find((t) => t.type === EventTypesEnum.NETWORK_REQUEST)!.title).toBe(
      `Network (${sessionStore.networkEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.CHANGED_URL)!.title).toBe(
      `URL (${sessionStore.changedUrlEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.ELEMENT_CLICKED)!.title).toBe(
      `Click (${sessionStore.clickEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.SCROLL)!.title).toBe(
      `Scroll (${sessionStore.scrollEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.RESIZE_SCREEN)!.title).toBe(
      `Resize (${sessionStore.resizedScreenEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.DATABASE_TRANSACTION)!.title).toBe(
      `Database (${sessionStore.databaseTransactionEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.LOG)!.title).toBe(
      `Logs (${sessionStore.logEvents.length})`
    )

    expect(tabs.value.find((t) => t.type === EventTypesEnum.CUSTOM_EVENT)!.title).toBe(
      `Custom (${sessionStore.customEvents.length})`
    )
  })
})
