import type { PropType } from 'vue'
import { defineComponent, h } from 'vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import NetworkEvent from '@/components/resources/events/listEvent/types/NetworkEvent.vue'
import Skeleton from 'primevue/skeleton'
import { range } from '@/services/number'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import ChangedUrl from '@/components/resources/events/listEvent/types/ChangedUrl.vue'
import ElementClick from '@/components/resources/events/listEvent/types/ElementClick.vue'
import ScrollEvent from '@/components/resources/events/listEvent/types/ScrollEvent.vue'
import ResizeScreenEvent from '@/components/resources/events/listEvent/types/ResizeScreenEvent.vue'
import DatabaseTransactionEvent from '@/components/resources/events/listEvent/types/DatabaseTransactionEvent.vue'
import LogEvent from '@/components/resources/events/listEvent/types/LogEvent.vue'
import CustomEvent from '@/components/resources/events/listEvent/types/CustomEvent.vue'

const EventsComponents: Record<EventTypesEnum, ReturnType<typeof defineComponent>> = {
  [EventTypesEnum.NETWORK_REQUEST]: NetworkEvent,
  [EventTypesEnum.CHANGED_URL]: ChangedUrl,
  [EventTypesEnum.ELEMENT_CLICKED]: ElementClick,
  [EventTypesEnum.SCROLL]: ScrollEvent,
  [EventTypesEnum.RESIZE_SCREEN]: ResizeScreenEvent,
  [EventTypesEnum.DATABASE_TRANSACTION]: DatabaseTransactionEvent,
  [EventTypesEnum.LOG]: LogEvent,
  [EventTypesEnum.CUSTOM_EVENT]: CustomEvent
} as const

export const ListEvents = defineComponent({
  name: 'ListEvents',
  props: {
    events: { type: Array as PropType<EventCodec[]>, required: true },
    session: { type: Object as PropType<SessionCodec>, required: true },
    loading: { type: Boolean, default: false },
    height: { type: Number, required: true }
  },
  emits: {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    'see:details': (event: EventCodec) => true
  },
  setup(props, { emit }) {
    const rangeLoading = range(0, 10)

    const onSeeDetails = (event: EventCodec) => {
      emit('see:details', event)
    }

    return function render() {
      if (props.loading) {
        return h('div', [
          rangeLoading.map(() => h(Skeleton, { class: 'mt-1', height: '40px', width: '100%' }))
        ])
      }

      return h(
        'div',
        props.events.map((event) =>
          h(EventsComponents[event.type], {
            event,
            session: props.session,
            key: event.id,
            'onSee:details': onSeeDetails
          })
        )
      )

      // @TODO: Make it work with virtual scroller
      // return h(
      //   VirtualScroller,
      //   {
      //     itemSize: 48,
      //     style: `width: 100%; height: ${props.height}px`,
      //     items: props.events,
      //     class: 'bg-red'
      //   },
      //   {
      //     item: ({ item }: { item: EventCodec }) =>
      //       h(EventsComponents[item.type], { event: item, session: props.session, key: item.id })
      //   }
      // )
    }
  }
})
