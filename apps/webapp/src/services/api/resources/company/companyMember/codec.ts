import type { DateTime, ResourceID } from '@/services/api'
import type { UserCodec } from '@/services/api/resources/user/codec'

export interface CompanyMemberCodec {
  id: ResourceID
  createdAt: DateTime
  invitedBy?: UserCodec
  member?: UserCodec | null
  registerToken?: { email: string } | null
}

export type InviteMembersToCompanyBody = { emails: string[] }
