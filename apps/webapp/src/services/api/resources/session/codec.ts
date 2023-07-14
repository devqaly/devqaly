import type { DateTime, ResourceID } from '@/services/api'
import { SessionVideoStatusEnum } from '@/services/api/resources/session/constants'
import type { UserCodec } from '@/services/api/resources/user/codec'
import type { ProjectCodec } from '@/services/api/resources/project/codec'

export interface SessionCodec {
  id: ResourceID
  endedVideoConversionAt: DateTime | null
  os: string | null
  windowHeight: number
  windowWidth: number
  platformName: string | null
  startedVideoConversionAt: DateTime | null
  version: string | null
  videoStatus: SessionVideoStatusEnum
  createdAt: DateTime
  videoDurationInSeconds: number
  videoUrl: string | null
  project?: ProjectCodec
  createdBy?: UserCodec
}

export type AssignSessionToUserBody = { userId: string }
