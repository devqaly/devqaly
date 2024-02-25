import { axios } from '@/services/api/axios'
import type { PaginatableRecord } from '@/services/api'
import type {
  CompanyMemberCodec,
  InviteMembersToCompanyBody
} from '@/services/api/resources/company/companyMember/codec'
import type { GetCompanyMembersParameters } from '@/services/api/resources/company/companyMember/requests'

export const getCompanyMembers = (companyId: string, params: GetCompanyMembersParameters = {}) =>
  axios.get<PaginatableRecord<CompanyMemberCodec>>(`/companies/${companyId}/members`, { params })

export const addMembersToCompany = (companyId: string, body: InviteMembersToCompanyBody) =>
  axios.post<null>(`/companies/${companyId}/members`, body)

export const removeMembersFromCompany = (companyId: string, companyMemberId: string) =>
  axios.delete<null>(`/companies/${companyId}/members/${companyMemberId}`)
