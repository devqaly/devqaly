import type { SessionCodec } from '@/services/api/resources/session/codec'
import { SessionVideoStatusEnum } from '@/services/api/resources/session/constants'

export const sessionsCodecFactory = (): SessionCodec => ({
  id: '',
  endedVideoConversionAt: null,
  windowWidth: 0,
  windowHeight: 0,
  os: null,
  platformName: null,
  startedVideoConversionAt: null,
  version: null,
  videoStatus: SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION,
  createdAt: '',
  videoDurationInSeconds: 0,
  videoUrl: null
})
