import { defineStore } from 'pinia'
import type { PaginatableRecord } from '@/services/api'
import type {
  CompanyMemberCodec,
  RemoveCompanyMemberBody
} from '@/services/api/resources/company/companyMember/codec'
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
    async removeUsersFromCompany(companyId: string, body: RemoveCompanyMemberBody) {
      await removeMembersFromCompany(companyId, body)

      if (body.users !== undefined) {
        this.companyMembersRequest.data = this.companyMembersRequest.data.filter((member) => {
          if (member.registerToken) return true

          return body.users && member.member && !body.users.includes(member.member.id)
        })
      }

      if (body.registerTokens !== undefined) {
        this.companyMembersRequest.data = this.companyMembersRequest.data.filter((member) => {
          if (member.member) return true

          return (
            body.registerTokens &&
            member.registerToken &&
            !body.registerTokens.includes(member.registerToken.id)
          )
        })
      }
    }
  }
})
