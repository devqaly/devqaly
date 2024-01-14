import { describe, expect, it, vi } from 'vitest'
import { emptyLinks, emptyMeta } from './api'
import type { Meta } from './api'
import { randomInteger } from './number'
import { copyToClipboard, getPaginationPropsForMeta, hasNextPage } from './ui'

const copyFn = vi.fn(() => new Promise<void>((resolve) => resolve()))

Object.defineProperty(navigator, 'clipboard', {
  writable: true,
  value: { writeText: copyFn }
})

describe('ui.ts', () => {
  it('should return correct pagination props for meta component when calling `getPaginationPropsForMeta` fn', () => {
    const meta: Meta = {
      ...emptyMeta(),
      currentPage: randomInteger(1, 10)
    }

    const perPage = randomInteger(1, 10)
    const props = getPaginationPropsForMeta(meta, perPage)

    expect(props.totalRecords).toEqual(meta.total)
    expect(props.rows).toEqual(perPage)
    expect(props.first).toEqual((meta.currentPage - 1) * perPage)
  })

  it('should tell if there is a next page in the request when calling `hasNextPage` fn', () => {
    const hasNextPageLink = {
      ...emptyLinks(),
      next: 'https://domain.com/api/users?page=2'
    }

    const resultHasNextPage = hasNextPage(hasNextPageLink)

    expect(resultHasNextPage).to.be.true

    const hasNoNextPage = {
      ...emptyLinks(),
      next: null
    }

    const resultHasNoNextPage = hasNextPage(hasNoNextPage)

    expect(resultHasNoNextPage).to.be.false
  })

  it('should call `navigator.clipboard.writeText` when calling `copyToClipboard` fn', async () => {
    const textToCopy = 'hello-its-me'

    await copyToClipboard(textToCopy)

    expect(copyFn).to.toHaveBeenCalledOnce()
  })
})
