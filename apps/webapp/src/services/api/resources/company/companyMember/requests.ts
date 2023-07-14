import type { PaginationParameters } from '@/services/api'
import { OrderBy } from '@/services/api'

export interface GetCompanyMembersParameters extends PaginationParameters {
  memberName?: string
  invitedByName?: string
  orderByCreatedAt?: OrderBy
}
