import { defineComponent, h } from 'vue'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import NetworkEventTab from './types/NetworkEventTab.vue'
import TabPanel from 'primevue/tabpanel'
import TabView from 'primevue/tabview'
import { useSessionsStore } from '@/stores/sessions'
import ChangedUrlTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/ChangedUrlTab.vue'
import ElementClickTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/ElementClickTab.vue'
import ScrollTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/ScrollTab.vue'
import ResizeScreenTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/ResizeScreenTab.vue'
import DatabaseTransactionTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/DatabaseTransactionTab.vue'
import { ActiveEventDetail } from '@/components/pages/projects/ProjectSessionView/EventTabs/ActiveEvent/ActiveEventDetail'
import LogEventTab from '@/components/pages/projects/ProjectSessionView/EventTabs/types/LogEventTab.vue'

const COMPONENTS: Record<EventTypesEnum, ReturnType<typeof defineComponent>> = {
  [EventTypesEnum.NETWORK_REQUEST]: NetworkEventTab,
  [EventTypesEnum.CHANGED_URL]: ChangedUrlTab,
  [EventTypesEnum.ELEMENT_CLICKED]: ElementClickTab,
  [EventTypesEnum.SCROLL]: ScrollTab,
  [EventTypesEnum.RESIZE_SCREEN]: ResizeScreenTab,
  [EventTypesEnum.DATABASE_TRANSACTION]: DatabaseTransactionTab,
  [EventTypesEnum.LOG]: LogEventTab
}

const TABS_TITLE: Record<EventTypesEnum, string> = {
  [EventTypesEnum.NETWORK_REQUEST]: 'Network Request',
  [EventTypesEnum.CHANGED_URL]: 'URL Changed',
  [EventTypesEnum.ELEMENT_CLICKED]: 'Clicked Element',
  [EventTypesEnum.SCROLL]: 'Scroll',
  [EventTypesEnum.RESIZE_SCREEN]: 'Resized Screen',
  [EventTypesEnum.DATABASE_TRANSACTION]: 'Database Transaction',
  [EventTypesEnum.LOG]: 'Logs'
}

const tabs: {
  type: EventTypesEnum
  'data-cy': string
  icon: string
  storeGetter:
    | 'networkEvents'
    | 'clickEvents'
    | 'changedUrlEvents'
    | 'scrollEvents'
    | 'resizedScreenEvents'
    | 'databaseTransactionEvents'
    | 'logEvents'
}[] = [
  {
    type: EventTypesEnum.NETWORK_REQUEST,
    'data-cy': 'project-session-view__activate-network-events-tab',
    icon: 'pi pi-desktop',
    storeGetter: 'networkEvents'
  },
  {
    type: EventTypesEnum.CHANGED_URL,
    'data-cy': 'project-session-view__activate-change-url-events-tab',
    icon: 'pi pi-at',
    storeGetter: 'changedUrlEvents'
  },
  {
    type: EventTypesEnum.ELEMENT_CLICKED,
    'data-cy': 'project-session-view__activate-click-events-tab',
    icon: 'pi pi-eye',
    storeGetter: 'clickEvents'
  },
  {
    type: EventTypesEnum.SCROLL,
    'data-cy': 'project-session-view__activate-scroll-events-tab',
    icon: 'pi pi-arrows-v',
    storeGetter: 'scrollEvents'
  },
  {
    type: EventTypesEnum.RESIZE_SCREEN,
    'data-cy': 'project-session-view__activate-resize-events-tab',
    icon: 'pi pi-arrows-h',
    storeGetter: 'resizedScreenEvents'
  },
  {
    type: EventTypesEnum.DATABASE_TRANSACTION,
    'data-cy': 'project-session-view__activate-db-transaction-events-tab',
    icon: 'pi pi-server',
    storeGetter: 'databaseTransactionEvents'
  },
  {
    type: EventTypesEnum.LOG,
    'data-cy': 'project-session-view__activate-log-events-tab',
    icon: 'pi pi-paperclip',
    storeGetter: 'logEvents'
  }
]

export const EventsSection = defineComponent({
  name: 'EventsSection',
  setup() {
    const sessionStore = useSessionsStore()

    return function render() {
      return h(
        TabView,
        {
          pt: { panelContainer: { class: 'p-0' } }
        },
        () =>
          tabs.map((tab) =>
            h(
              TabPanel,
              {},
              {
                default: () =>
                  h('div', { class: 'grid' }, [
                    h(
                      'div',
                      {
                        class: {
                          'max-h-30rem overflow-y-auto': true,
                          'col-8': sessionStore.activeEventDetails !== null,
                          'col-12': sessionStore.activeEventDetails === null
                        }
                      },
                      h(COMPONENTS[tab.type], {})
                    ),
                    sessionStore.activeEventDetails &&
                      h(
                        'div',
                        { class: 'col-4 mt-2' },
                        h(ActiveEventDetail, { event: sessionStore.activeEventDetails })
                      )
                  ]),
                header: () =>
                  h('div', [
                    h('i', { class: `${tab.icon} mr-2`, 'data-cy': tab['data-cy'] }),
                    h('span', `${TABS_TITLE[tab.type]} (${sessionStore[tab.storeGetter].length})`)
                  ])
              }
            )
          )
      )
    }
  }
})
