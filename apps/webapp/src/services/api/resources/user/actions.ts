import { axios } from '@/services/api/axios'
import type { BaseSingleResource } from '@/services/api'
import type { LoggedUserCodec } from '@/services/api/resources/user/codec'

export const getLoggedUserInformation = () =>
  axios.get<BaseSingleResource<LoggedUserCodec>>('/users/me')
