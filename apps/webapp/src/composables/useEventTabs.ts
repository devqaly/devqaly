import { useSessionsStore } from '@/stores/sessions'
import { computed } from 'vue'
import type { TabsPropType } from '@/components/resources/events/EventTabs/EventsSection'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'

export function useEventTabs() {
  const sessionStore = useSessionsStore()

  const tabs = computed<TabsPropType>(() => [
    {
      type: EventTypesEnum.NETWORK_REQUEST,
      'data-cy': 'project-session-view__activate-network-events-tab',
      icon: 'pi pi-desktop',
      title: `Network (${sessionStore.networkEvents.length})`
    },
    {
      type: EventTypesEnum.CHANGED_URL,
      'data-cy': 'project-session-view__activate-change-url-events-tab',
      icon: 'pi pi-at',
      storeGetter: 'changedUrlEvents',
      title: `URL (${sessionStore.changedUrlEvents.length})`
    },
    {
      type: EventTypesEnum.ELEMENT_CLICKED,
      'data-cy': 'project-session-view__activate-click-events-tab',
      icon: 'pi pi-eye',
      title: `Click (${sessionStore.clickEvents.length})`
    },
    {
      type: EventTypesEnum.SCROLL,
      'data-cy': 'project-session-view__activate-scroll-events-tab',
      icon: 'pi pi-arrows-v',
      title: `Scroll (${sessionStore.scrollEvents.length})`
    },
    {
      type: EventTypesEnum.RESIZE_SCREEN,
      'data-cy': 'project-session-view__activate-resize-events-tab',
      icon: 'pi pi-arrows-h',
      title: `Resize (${sessionStore.resizedScreenEvents.length})`
    },
    {
      type: EventTypesEnum.DATABASE_TRANSACTION,
      'data-cy': 'project-session-view__activate-db-transaction-events-tab',
      icon: 'pi pi-server',
      title: `Database (${sessionStore.databaseTransactionEvents.length})`
    },
    {
      type: EventTypesEnum.LOG,
      'data-cy': 'project-session-view__activate-log-events-tab',
      icon: 'pi pi-paperclip',
      title: `Logs (${sessionStore.logEvents.length})`
    },
    {
      type: EventTypesEnum.CUSTOM_EVENT,
      'data-cy': 'project-session-view__activate-custom-events-tab',
      icon: 'pi pi-paperclip',
      title: `Custom (${sessionStore.customEvents.length})`
    }
  ])

  return { tabs }
}
