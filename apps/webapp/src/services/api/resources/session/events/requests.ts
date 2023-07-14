import type { DateTime, PaginationParameters } from '@/services/api'

export interface GetSessionEventsParameters extends PaginationParameters {
  startCreatedAt?: DateTime
  endCreatedAt?: DateTime
}

export type GetDatabaseRequestForNetworkRequestParameters = PaginationParameters
export type GetLogsForNetworkRequestParameters = PaginationParameters
