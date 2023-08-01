import type { ResourceID } from '@/services/api'

export interface LoggedUserCodec {
  id: ResourceID
  firstName: string
  lastName: string
  timezone: string
  fullName: string
}

export interface UserCodec {
  id: ResourceID
  firstName: string
  lastName: string
  timezone: string
  fullName: string
}
