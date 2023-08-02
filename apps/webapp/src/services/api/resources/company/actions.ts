import { axios } from '@/services/api/axios'
import type { CreateCompanyBody } from '@/services/api/resources/company/requests'
import type { BaseSingleResource } from '@/services/api'
import type { CompanyCodec } from '@/services/api/resources/company/codec'

export const createCompany = (body: CreateCompanyBody) =>
  axios.post<BaseSingleResource<CompanyCodec>>(`/companies`, body)
