import { axios } from '@/services/api/axios'
import type {
  CreateCompanyBody,
  GetCompanyStripePortalRequest,
  UpdateCompanyBillingDetailsBody,
  UpdateCompanySubscriptionBody
} from '@/services/api/resources/company/requests'
import type { BaseSingleResource } from '@/services/api'
import type {
  CompanyCodec,
  CompanyStripePortalResponse
} from '@/services/api/resources/company/codec'

export const createCompany = (body: CreateCompanyBody) =>
  axios.post<BaseSingleResource<CompanyCodec>>(`/companies`, body)

export const getCompanyStripePortalUrl = (
  companyId: string,
  params: GetCompanyStripePortalRequest
) =>
  axios.get<BaseSingleResource<CompanyStripePortalResponse>>(
    `/companies/${companyId}/stripe/portal`,
    { params }
  )

export const updateCompanyBillingDetails = (
  companyId: string,
  body: UpdateCompanyBillingDetailsBody
) => axios.put<BaseSingleResource<CompanyCodec>>(`/companies/${companyId}/billingDetails`, body)

export const updateCompanySubscription = (companyId: string, body: UpdateCompanySubscriptionBody) =>
  axios.put<BaseSingleResource<CompanyCodec>>(`/companies/${companyId}/subscription`, body)
