import { describe, expect, it } from 'vitest'
import { formatToDate, formatToDateTime } from './date'

describe('date.ts', () => {
  it('should return correct format when calling `formatToDateTime`', () => {
    const date1 = new Date(2022, 1, 1, 10, 10, 10)
    let formattedDate = formatToDateTime(date1)

    expect(formattedDate).toBe('01/02/2022 10:10')

    const date2 = new Date(2010, 11, 1, 10, 10, 10)
    formattedDate = formatToDateTime(date2)

    expect(formattedDate).toBe('01/12/2010 10:10')
  })

  it('should return correct format when calling `formatToDate`', () => {
    const date1 = new Date(2022, 1, 1, 10, 10, 10)
    let formattedDate = formatToDate(date1)

    expect(formattedDate).toBe('01/02/2022')

    const date2 = new Date(2010, 11, 1, 10, 10, 10)
    formattedDate = formatToDate(date2)

    expect(formattedDate).toBe('01/12/2010')
  })
})
