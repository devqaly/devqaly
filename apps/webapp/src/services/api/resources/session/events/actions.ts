import { axios } from '@/services/api/axios'
import type { PaginatableRecord } from '@/services/api'
import type {
  DatabaseTransactionEvent,
  EventCodec,
  LogEvent
} from '@/services/api/resources/session/events/codec'
import type {
  GetDatabaseRequestForNetworkRequestParameters,
  GetLogsForNetworkRequestParameters,
  GetSessionEventsParameters
} from '@/services/api/resources/session/events/requests'

export const getEventsForSession = (sessionId: string, params: GetSessionEventsParameters = {}) =>
  axios.get<PaginatableRecord<EventCodec>>(`sessions/${sessionId}/events`, { params })

export const getDatabaseTransactionsForRequestId = (
  requestId: string,
  params: GetDatabaseRequestForNetworkRequestParameters = {}
) =>
  axios.get<PaginatableRecord<DatabaseTransactionEvent>>(
    `events/networkRequest/byRequestId/${requestId}/databaseTransactions`,
    { params }
  )

export const getLogsForRequestId = (
  requestId: string,
  params: GetLogsForNetworkRequestParameters = {}
) =>
  axios.get<PaginatableRecord<LogEvent>>(`events/networkRequest/byRequestId/${requestId}/logs`, {
    params
  })
