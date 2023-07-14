// @ts-ignore
import mitt, { Emitter } from 'mitt'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

export enum EventBusEvents {
  REQUEST_CLICKED = 'requestClicked'
}

type Events = {
  [EventBusEvents.REQUEST_CLICKED]: EventCodec
}

export const eventBus: Emitter<Events> = mitt<Events>()
