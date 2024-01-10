import type { PropType } from 'vue'
import { defineComponent, h } from 'vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import ChangeUrlDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/ChangeUrlDetails.vue'
import DatabaseTransactionDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/DatabaseTransactionDetails.vue'
import ElementClickDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/ElementClickDetails.vue'
import NetworkEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/NetworkEventDetails.vue'
import ResizeScreenDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/ResizeScreenDetails.vue'
import ScrollTabDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/ScrollTabDetails.vue'
import LogEventDetails from '@/components/resources/events/EventTabs/ActiveEvent/types/LogEventDetails.vue'
import CustomEvent from '@/components/resources/events/EventTabs/ActiveEvent/types/CustomEvent.vue'

const ACTIVE_EVENT_DETAILS: Record<EventTypesEnum, ReturnType<typeof defineComponent>> = {
  [EventTypesEnum.CHANGED_URL]: ChangeUrlDetails,
  [EventTypesEnum.DATABASE_TRANSACTION]: DatabaseTransactionDetails,
  [EventTypesEnum.ELEMENT_CLICKED]: ElementClickDetails,
  [EventTypesEnum.NETWORK_REQUEST]: NetworkEventDetails,
  [EventTypesEnum.RESIZE_SCREEN]: ResizeScreenDetails,
  [EventTypesEnum.SCROLL]: ScrollTabDetails,
  [EventTypesEnum.LOG]: LogEventDetails,
  [EventTypesEnum.CUSTOM_EVENT]: CustomEvent
} as const

export const ActiveEventDetail = defineComponent({
  name: 'ActiveEventDetail',
  props: {
    event: { type: Object as PropType<EventCodec>, required: true }
  },
  setup(props) {
    return function render() {
      return h(ACTIVE_EVENT_DETAILS[props.event.type], { event: props.event })
    }
  }
})
