import { defineComponent, h } from 'vue'
import type { PropType } from 'vue'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import NetworkEventTab from './types/NetworkEventTab.vue'
import TabPanel from 'primevue/tabpanel'
import TabView from 'primevue/tabview'
import ChangedUrlTab from '@/components/resources/events/EventTabs/types/ChangedUrlTab.vue'
import ElementClickTab from '@/components/resources/events/EventTabs/types/ElementClickTab.vue'
import ScrollTab from '@/components/resources/events/EventTabs/types/ScrollTab.vue'
import ResizeScreenTab from '@/components/resources/events/EventTabs/types/ResizeScreenTab.vue'
import DatabaseTransactionTab from '@/components/resources/events/EventTabs/types/DatabaseTransactionTab.vue'
import { ActiveEventDetail } from '@/components/resources/events/EventTabs/ActiveEvent/ActiveEventDetail'
import LogEventTab from '@/components/resources/events/EventTabs/types/LogEventTab.vue'
import CustomEventTab from '@/components/resources/events/EventTabs/types/CustomEventTab.vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

const COMPONENTS: Record<EventTypesEnum, ReturnType<typeof defineComponent>> = {
  [EventTypesEnum.NETWORK_REQUEST]: NetworkEventTab,
  [EventTypesEnum.CHANGED_URL]: ChangedUrlTab,
  [EventTypesEnum.ELEMENT_CLICKED]: ElementClickTab,
  [EventTypesEnum.SCROLL]: ScrollTab,
  [EventTypesEnum.RESIZE_SCREEN]: ResizeScreenTab,
  [EventTypesEnum.DATABASE_TRANSACTION]: DatabaseTransactionTab,
  [EventTypesEnum.LOG]: LogEventTab,
  [EventTypesEnum.CUSTOM_EVENT]: CustomEventTab
}

export type TabsPropType = {
  type: EventTypesEnum
  title: string
  'data-cy': string
  icon: string
}[]

export const EventsSection = defineComponent({
  name: 'EventsSection',
  props: {
    activeEventDetails: { type: Object as PropType<null | EventCodec> },
    tabs: {
      type: Object as PropType<TabsPropType>,
      required: true
    }
  },
  setup(props) {
    return function render() {
      return h(
        TabView,
        {
          pt: { panelContainer: { class: '!p-0' } }
        },
        () =>
          props.tabs.map((tab) =>
            h(
              TabPanel,
              {},
              {
                default: () =>
                  h('div', { class: 'grid grid-cols-12 gap-5' }, [
                    h(
                      'div',
                      {
                        class: {
                          'max-h-30rem overflow-y-auto': true,
                          'col-span-8': props.activeEventDetails !== null,
                          'col-span-12': props.activeEventDetails === null
                        }
                      },
                      h(COMPONENTS[tab.type], {})
                    ),
                    props.activeEventDetails &&
                      h(
                        'div',
                        { class: 'col-span-4 mt-2' },
                        h(ActiveEventDetail, { event: props.activeEventDetails })
                      )
                  ]),
                header: () =>
                  h('div', [
                    h('i', { class: `${tab.icon} mr-2`, 'data-cy': tab['data-cy'] }),
                    h('span', tab.title)
                  ])
              }
            )
          )
      )
    }
  }
})
