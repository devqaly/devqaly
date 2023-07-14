import type { ResourceID } from '@/services/api'
import type { CompanyCodec } from '@/services/api/resources/company/codec'

export interface LoggedUserCodec {
  id: ResourceID
  firstName: string
  lastName: string
  timezone: string
  fullName: string
  company: Pick<CompanyCodec, 'id'>
}

export interface UserCodec {
  id: ResourceID
  firstName: string
  lastName: string
  timezone: string
  fullName: string
}
