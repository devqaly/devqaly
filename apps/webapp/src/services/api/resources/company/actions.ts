import { axios } from '@/services/api/axios'
import type { CreateCompanyBody } from '@/services/api/resources/company/requests'
import type { BaseSingleResource } from '@/services/api'
import type {
  CompanyCodec,
  CompanyStripePortalResponse
} from '@/services/api/resources/company/codec'

export const createCompany = (body: CreateCompanyBody) =>
  axios.post<BaseSingleResource<CompanyCodec>>(`/companies`, body)

export const getCompanyStripePortalUrl = (companyId: string) =>
  axios.get<BaseSingleResource<CompanyStripePortalResponse>>(
    `/companies/${companyId}/stripe/portal`
  )
