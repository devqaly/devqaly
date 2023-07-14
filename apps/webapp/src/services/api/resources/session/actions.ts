import { axios } from '@/services/api/axios'
import type { BaseSingleResource } from '@/services/api'
import type { AssignSessionToUserBody, SessionCodec } from '@/services/api/resources/session/codec'

export const getSession = (sessionId: string) =>
  axios.get<BaseSingleResource<SessionCodec>>(`sessions/${sessionId}`)

export const assignSessionToUser = (sessionId: string, body: AssignSessionToUserBody) =>
  axios.post<BaseSingleResource<SessionCodec>>(`sessions/${sessionId}/assign`, body)
