import { defineStore } from 'pinia'
import type { PaginatableRecord } from '@/services/api'
import type { CompanyMemberCodec } from '@/services/api/resources/company/companyMember/codec'
import { emptyPagination } from '@/services/api'
import {
  getCompanyMembers,
  removeMembersFromCompany
} from '@/services/api/resources/company/companyMember/actions'
import type { GetCompanyMembersParameters } from '@/services/api/resources/company/companyMember/requests'

interface CompanyMembersStoreState {
  companyMembersRequest: PaginatableRecord<CompanyMemberCodec>
}

export const useCompanyMembersStore = defineStore('companyMembersStore', {
  state: (): CompanyMembersStoreState => ({
    companyMembersRequest: emptyPagination()
  }),
  actions: {
    async fetchCompanyMembers(companyId: string, params: GetCompanyMembersParameters = {}) {
      const { data } = await getCompanyMembers(companyId, params)

      this.companyMembersRequest = data
    },
    async removeUsersFromCompany(companyId: string, companyMemberId: string) {
      await removeMembersFromCompany(companyId, companyMemberId)

      this.companyMembersRequest.data = this.companyMembersRequest.data.filter(
        (c) => c.id !== companyMemberId
      )
    }
  }
})
