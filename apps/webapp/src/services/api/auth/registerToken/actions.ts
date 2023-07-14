import { axios } from '@/services/api/axios'
import type {
  CreateRegisterTokenBody,
  ResendEmailRegisterTokenBody,
  UpdateRegisterTokenBody,
  UpdateRegisterTokenResponseBody
} from '@/services/api/auth/registerToken/codec'
import type { BaseSingleResource } from '@/services/api'

export const createRegisterToken = (body: CreateRegisterTokenBody) =>
  axios.post<null>(`registerTokens`, body)

export const resendEmailRegisterToken = (body: ResendEmailRegisterTokenBody) =>
  axios.post<null>(`registerTokens/resendEmail`, body)

export const updateRegisterToken = (token: string, body: UpdateRegisterTokenBody) =>
  axios.put<BaseSingleResource<UpdateRegisterTokenResponseBody>>(`registerTokens/${token}`, body)
