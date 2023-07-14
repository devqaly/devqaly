import { defineStore } from 'pinia'
import type { PaginatableRecord } from '@/services/api'
import type { CompanyMemberCodec } from '@/services/api/resources/company/companyMember/codec'
import { emptyPagination } from '@/services/api'
import { getCompanyMembers } from '@/services/api/resources/company/companyMember/actions'
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
    }
  }
})
