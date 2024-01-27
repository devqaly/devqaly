import type { DateTime, PaginationParameters, ResourceID } from '@/services/api'
import type { UserCodec } from '@/services/api/resources/user/codec'
import { OrderBy } from '@/services/api'
import type { CompanyCodec } from '@/services/api/resources/company/codec'

export interface ProjectCodec {
  id: ResourceID
  title: string
  projectKey: string
  securityToken: string
  createdBy?: UserCodec
  createdAt: DateTime
  company?: CompanyCodec
}

export type CreateProjectBody = { title: string }

export type CreatableProject = CreateProjectBody

export interface GetProjectsParameters extends PaginationParameters {
  loadUrls?: boolean
  title?: string
}

export interface GetProjectSessionsParameters extends PaginationParameters {
  createdAtOrder?: OrderBy
  createdByName?: string
  os?: string
  platform?: string
  version?: string
}
