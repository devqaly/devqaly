import { axios } from '@/services/api/axios'
import type { BaseSingleResource, PaginatableRecord } from '@/services/api'
import type { LoggedUserCodec } from '@/services/api/resources/user/codec'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import type { GetLoggedUserCompaniesParameters } from '@/services/api/resources/user/requests'

export const getLoggedUserInformation = () =>
  axios.get<BaseSingleResource<LoggedUserCodec>>('/users/me')

export const getLoggedUserCompanies = (
  userId: string,
  params: GetLoggedUserCompaniesParameters = {}
) => axios.get<PaginatableRecord<CompanyCodec>>(`users/${userId}/companies`, { params })
