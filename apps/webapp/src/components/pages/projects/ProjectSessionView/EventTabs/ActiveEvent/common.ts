import type { EventCodec } from '@/services/api/resources/session/events/codec'

export type Props<T extends EventCodec> = {
  event: T
}
