import type { SessionCodec } from '@/services/api/resources/session/codec'
import { SessionVideoStatusEnum } from '@/services/api/resources/session/constants'
import { intervalToDuration, isWithinInterval } from 'date-fns'
import { range } from '@/services/number'
import addSeconds from 'date-fns/addSeconds'
import subSeconds from 'date-fns/subSeconds'
import type { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

export const isVideoQueued = (state: SessionCodec['videoStatus']) =>
  state === SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION

export const isVideoBeingConverted = (state: SessionCodec['videoStatus']) =>
  state === SessionVideoStatusEnum.CONVERTING

export const isVideoConverted = (state: SessionCodec['videoStatus']) =>
  state === SessionVideoStatusEnum.CONVERTED

const zeroPad = (num: number) => String(num).padStart(2, '0')

export const translateVideoDuration = (durationInSeconds: number) => {
  const duration = intervalToDuration({ start: 0, end: durationInSeconds * 1000 })

  if (duration.hours !== undefined && duration.hours > 0) {
    return `${zeroPad(duration.hours!)}:${zeroPad(duration.minutes!)}:${zeroPad(duration.seconds!)}`
  }

  if (duration.minutes !== undefined && duration.minutes > 0) {
    return `00:${zeroPad(duration.minutes!)}:${zeroPad(duration.seconds!)}`
  }

  return `00:00:${zeroPad(duration.seconds!)}`
}

export const getVideoStatusText = (status: SessionVideoStatusEnum) => {
  switch (status) {
    case SessionVideoStatusEnum.CONVERTED:
      return 'Converted'
    case SessionVideoStatusEnum.CONVERTING:
      return 'Converting'
    case SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION:
      return 'In Queue'
    default:
      throw new Error(`\`status\` should be of SessionVideoStatusEnum. Received: ${status}`)
  }
}

export const getVideoStatusColor = (status: SessionVideoStatusEnum) => {
  switch (status) {
    case SessionVideoStatusEnum.CONVERTED:
      return '#1EA97C'
    case SessionVideoStatusEnum.CONVERTING:
      return '#CC8925'
    case SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION:
      return '#495057'
    default:
      throw new Error(`\`status\` should be of SessionVideoStatusEnum. Received: ${status}`)
  }
}

/**
 * Slice the video time into partitions of `partitionSize`.
 * E.g. Imagine we have a video of 37 seconds. We would like to create X partitions of 10 seconds.
 * Therefore, we would like to create 4 partitions for this video.
 *
 *  ---------------------------------------------------------------------------------
 * |  1st partition   |   2nd partition   |   3rd partition   |   4th partition     |
 *  ---------------------------------------------------------------------------------
 * ↑ 00:00            ↑ 00:10             ↑ 00:20             ↑ 00:30               ↑ 00:37
 *
 * @param durationInSeconds
 * @param partitionSize
 */
export const createPartitionsForVideo = (durationInSeconds: number, partitionSize: number) => {
  if (partitionSize < 1) {
    throw new Error(`partitionSize must be higher than 1. Received: ${partitionSize}`)
  }
  const numberPartitions = Math.ceil(durationInSeconds / partitionSize)

  return range(0, numberPartitions).reduce((accumulator, partition) => {
    return { ...accumulator, [partition]: { hasFetchedEvents: false } }
  }, {} as Record<number, { hasFetchedEvents: boolean }>)
}

/**
 * Find the startDate and endDate for partition based on the start date of the video.
 *
 * @param partition
 * @param partitionSize
 * @param videoStartedAt
 */
export const findStartAndEndDateForVideoPartition = (
  partition: number,
  partitionSize: number,
  videoStartedAt: SessionCodec['createdAt']
) => {
  //  ---------------------------------------------------------------------------------
  // |  1st partition   |   2nd partition   |   3rd partition   |   4th partition     |
  //  ---------------------------------------------------------------------------------
  // ↑ 00:00            ↑ 00:10             ↑ 00:20             ↑ 00:30               ↑ 00:37
  // =========================================================================================
  // Having the `partitionSize` and `partition` we can find the start and end seconds for this partition.
  // To get the end for 2nd partition, we can multiply `partitionSize` with `partition`.
  // To get the start for 2nd partition, we just need to subtract `partitionSize` from the calculation above.
  const deltaEndSeconds = partitionSize * partition
  const deltaStartSeconds = deltaEndSeconds - partitionSize

  const videoStart = new Date(videoStartedAt)
  const startDelta = addSeconds(videoStart, deltaStartSeconds)
  const endDelta = addSeconds(videoStart, deltaEndSeconds)

  return { startDelta, endDelta }
}

export const filterEventsUntilVideoCurrentTime = (
  event: EventCodec,
  videoCurrentTimeInSeconds: number,
  eventType: EventTypesEnum,
  videoStartedAt: SessionCodec['createdAt']
) => {
  if (eventType !== event.type) return false

  // Now that we have only events that are interesting to us,
  // we can fetch events that are 2 seconds into the future
  // ====================================================================================
  //  ---------------------------------------------------------------------------------
  // |                                                                                 |
  //  ---------------------------------------------------------------------------------
  // ↑ videoStart                                 ↑ videoCurrentTime (startDelta)
  // | We are interested in all events until here |
  // ====================================================================================
  const videoStart = new Date(videoStartedAt)
  const startDelta = addSeconds(
    videoStart,
    // If an event happens at the first second of the recording, we want to get that event as well.
    // This is important when the page is loaded and the video is not being played yet.
    videoCurrentTimeInSeconds < 1 ? 2 : videoCurrentTimeInSeconds + 2
  )
  const eventCreatedAt = new Date(event.clientUtcEventCreatedAt)

  return isWithinInterval(eventCreatedAt, {
    start: videoStart,
    end: startDelta
  })
}

export const filterLiveEvents = (
  event: EventCodec,
  videoStartedAt: SessionCodec['createdAt'],
  currentVideoTimeInSeconds: number
) => {
  const currentVideoTime = addSeconds(new Date(videoStartedAt), currentVideoTimeInSeconds)
  const twoSecondsAgo = subSeconds(currentVideoTime, 2)
  const twoSecondsAhead = addSeconds(currentVideoTime, 2)

  return isWithinInterval(new Date(event.clientUtcEventCreatedAt), {
    start: twoSecondsAgo,
    end: twoSecondsAhead
  })
}
