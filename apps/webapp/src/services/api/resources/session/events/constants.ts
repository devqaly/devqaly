export enum EventTypesEnum {
  NETWORK_REQUEST = 'App\\Models\\Session\\Event\\EventNetworkRequest',
  CHANGED_URL = 'App\\Models\\Session\\Event\\EventUrlChanged',
  ELEMENT_CLICKED = 'App\\Models\\Session\\Event\\EventElementClick',
  SCROLL = 'App\\Models\\Session\\Event\\EventElementScroll',
  RESIZE_SCREEN = 'App\\Models\\Session\\Event\\EventResizeScreen',
  DATABASE_TRANSACTION = 'App\\Models\\Session\\Event\\EventDatabaseTransaction',
  LOG = 'App\\Models\\Session\\Event\\EventLog',
  CUSTOM_EVENT = 'App\\Models\\Session\\Event\\EventCustomEvent'
}

export enum LOG_LEVEL {
  EMERGENCY = 'emergency',
  ALERT = 'alert',
  CRITICAL = 'critical',
  ERROR = 'error',
  WARNING = 'warning',
  NOTICE = 'notice',
  INFORMATIONAL = 'informational',
  DEBUG = 'debug'
}
