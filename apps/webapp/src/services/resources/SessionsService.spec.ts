import { describe, expect, it } from 'vitest'
import {
  createPartitionsForVideo,
  filterEventsUntilVideoCurrentTime,
  filterLiveEvents,
  findStartAndEndDateForVideoPartition,
  getVideoStatusColor,
  getVideoStatusText,
  isVideoBeingConverted,
  isVideoConverted,
  isVideoQueued,
  translateVideoDuration
} from '@/services/resources/SessionsService'
import { SessionVideoStatusEnum } from '@/services/api/resources/session/constants'
import addSeconds from 'date-fns/addSeconds'
import subSeconds from 'date-fns/subSeconds'
import { EventTypesEnum } from '@/services/api/resources/session/events/constants'
import type { EventCodec } from '@/services/api/resources/session/events/codec'

describe('SessionService.ts', () => {
  it('should return true when video is converting by calling `isVideoQueued`', () => {
    expect(isVideoQueued(SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION)).to.be.true
    expect(isVideoQueued(SessionVideoStatusEnum.CONVERTED)).to.be.false
    expect(isVideoQueued(SessionVideoStatusEnum.CONVERTING)).to.be.false
  })

  it('should return true when video is being converted by calling `isVideoBeingConverted`', () => {
    expect(isVideoBeingConverted(SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION)).to.be.false
    expect(isVideoBeingConverted(SessionVideoStatusEnum.CONVERTED)).to.be.false
    expect(isVideoBeingConverted(SessionVideoStatusEnum.CONVERTING)).to.be.true
  })

  it('should return true when video is converted by calling `isVideoConverted`', () => {
    expect(isVideoConverted(SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION)).to.be.false
    expect(isVideoConverted(SessionVideoStatusEnum.CONVERTED)).to.be.true
    expect(isVideoConverted(SessionVideoStatusEnum.CONVERTING)).to.be.false
  })

  it('should correctly return translated video duration', () => {
    const durationInSeconds = translateVideoDuration(10)

    expect(durationInSeconds).to.be.eq('00:00:10')

    const durationWithMinutes = translateVideoDuration(65)

    expect(durationWithMinutes).to.be.eq('00:01:05')

    const durationWithHours = translateVideoDuration(60 * 60 + 5)

    expect(durationWithHours).to.be.eq('01:00:05')
  })

  it('should return correct text for when getting video status to text', () => {
    const convertedText = 'Converted'
    const convertingText = 'Converting'
    const inQueueText = 'In Queue'

    expect(getVideoStatusText(SessionVideoStatusEnum.CONVERTED)).to.be.eq(convertedText)
    expect(getVideoStatusText(SessionVideoStatusEnum.CONVERTING)).to.be.eq(convertingText)
    expect(getVideoStatusText(SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION)).to.be.eq(inQueueText)
  })

  it('should return correct color for video status', () => {
    const convertedText = '#1EA97C'
    const convertingText = '#CC8925'
    const inQueueText = '#495057'

    expect(getVideoStatusColor(SessionVideoStatusEnum.CONVERTED)).to.be.eq(convertedText)
    expect(getVideoStatusColor(SessionVideoStatusEnum.CONVERTING)).to.be.eq(convertingText)
    expect(getVideoStatusColor(SessionVideoStatusEnum.IN_QUEUE_FOR_CONVERSION)).to.be.eq(
      inQueueText
    )
  })

  it('should return the correct number of partitions', () => {
    expect(() => createPartitionsForVideo(10, 0)).to.toThrowError()

    const singlePartition = createPartitionsForVideo(10, 10)

    expect(singlePartition).toStrictEqual({ 1: { hasFetchedEvents: false } })

    const twoPartitions = createPartitionsForVideo(20, 10)

    expect(twoPartitions).toStrictEqual({
      1: { hasFetchedEvents: false },
      2: { hasFetchedEvents: false }
    })

    const singlePartitionWithLowerDurationThanPartitionSize = createPartitionsForVideo(5, 10)

    expect(singlePartitionWithLowerDurationThanPartitionSize).toStrictEqual({
      1: { hasFetchedEvents: false }
    })
  })

  it('should return correct start and end date given the video partition', () => {
    const partition = 2
    const partitionSize = 10
    const videoStartedAt = '2024-01-14T00:00:00Z' // replace with an actual date string

    const expectedStart = addSeconds(new Date(videoStartedAt), 10)
    const expectedEnd = addSeconds(new Date(videoStartedAt), 20)

    const result = findStartAndEndDateForVideoPartition(partition, partitionSize, videoStartedAt)

    expect(result.startDelta).toEqual(expectedStart)
    expect(result.endDelta).toEqual(expectedEnd)
  })

  it('should return correct events until video current time', () => {
    const eventType = EventTypesEnum.NETWORK_REQUEST
    const videoCurrentTimeInSeconds = 5
    const videoStartedAt = '2024-01-14T00:00:00Z'

    const validEvent: EventCodec = {
      id: '1',
      createdAt: '2024-01-14T00:00:03Z', // event 3 seconds after video start
      clientUtcEventCreatedAt: '2024-01-14T00:00:03Z', // same as createdAt for simplicity
      source: 'source',
      type: eventType,
      event: {} as any
    }

    const invalidEvent: EventCodec = {
      id: '2',
      createdAt: '2024-01-14T00:00:06Z', // event 6 seconds after video start
      clientUtcEventCreatedAt: '2024-01-14T00:00:06Z', // same as createdAt for simplicity
      source: 'source',
      type: EventTypesEnum.ELEMENT_CLICKED,
      event: {} as any
    }

    const validResult = filterEventsUntilVideoCurrentTime(
      validEvent,
      videoCurrentTimeInSeconds,
      eventType,
      videoStartedAt
    )

    const invalidResult = filterEventsUntilVideoCurrentTime(
      invalidEvent,
      videoCurrentTimeInSeconds,
      eventType,
      videoStartedAt
    )

    expect(validResult).toBe(true)
    expect(invalidResult).toBe(false)
  })

  it('should return if an event is considered a live event', () => {
    const videoStartedAt = '2024-01-14T00:00:00Z' // replace with an actual date string
    const currentVideoTimeInSeconds = 10

    const validEvent: EventCodec = {
      id: '1',
      createdAt: '2024-01-14T00:00:12Z', // event 12 seconds after video start
      clientUtcEventCreatedAt: '2024-01-14T00:00:12Z', // same as createdAt for simplicity
      source: 'source',
      type: EventTypesEnum.NETWORK_REQUEST,
      event: {} as any
    }

    // Mock an event that should not pass the filter
    const invalidEvent: EventCodec = {
      id: 'eventId',
      createdAt: '2024-01-14T00:00:05Z', // event 5 seconds after video start
      clientUtcEventCreatedAt: '2024-01-14T00:00:05Z', // same as createdAt for simplicity
      source: 'source',
      type: EventTypesEnum.ELEMENT_CLICKED,
      event: {} as any
    }

    const validResult = filterLiveEvents(validEvent, videoStartedAt, currentVideoTimeInSeconds)

    const invalidResult = filterLiveEvents(invalidEvent, videoStartedAt, currentVideoTimeInSeconds)

    expect(validResult).toBe(true)
    expect(invalidResult).toBe(false)
  })
})
