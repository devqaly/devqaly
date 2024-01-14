import { describe, expect, it } from 'vitest'
import { randomInteger, range } from './number'

describe('number.ts', () => {
  it('should return correct range', () => {
    const positiveRangeReturned = range(0, 5)

    expect(positiveRangeReturned.sort()).toEqual([0, 1, 2, 3, 4, 5].sort())

    const negativeRangeReturned = range(-5, 0)

    expect(negativeRangeReturned).toEqual([-5, -4, -3, -2, -1, 0])
  })

  it('should return a random integer', () => {
    for (let i = 0; i < 15; i++) {
      const min = 1
      const max = 10
      const selectedRandomInteger = randomInteger(min, max)

      expect(selectedRandomInteger >= min && selectedRandomInteger <= max).toBeTruthy()
    }
  })
})
