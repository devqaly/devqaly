import type {
  LoginBody,
  LoginResponseBody,
  RequestPasswordLinkBody,
  ResetPasswordBody
} from '@/services/api/auth/codec'
import { axios } from '@/services/api/axios'

export const loginUser = (body: LoginBody) => axios.post<LoginResponseBody>(`/login`, body)

export const logoutUser = () => axios.post<null>('/logout')

export const requestPasswordResetLink = (body: RequestPasswordLinkBody) =>
  axios.post<null>('/resetPassword/link', body)

export const resetPassword = (body: ResetPasswordBody) => axios.post<null>('/resetPassword', body)
