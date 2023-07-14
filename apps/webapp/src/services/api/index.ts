export type ResourceID = string

export type UUID = string

export type DateTime = string

export interface BaseSingleResource<T> {
  data: T
}

export enum OrderBy {
  DESC = 'desc',
  ASC = 'asc'
}

export interface PaginationParameters {
  perPage?: number | string
  page?: number | string
}

export interface Links {
  first: string
  last: string
  prev: string | null
  next: string | null
}

export interface Meta {
  currentPage: number
  from: number
  lastPage: number
  path: string
  perPage: number
  to: number
  total: number
}

export interface PaginatableRecord<T> {
  data: T[]
  links: Links
  meta: Meta
}

const emptyLinks = (): Links => ({
  first: '',
  last: '',
  next: null,
  prev: null
})

const emptyMeta = (): Meta => ({
  currentPage: 1,
  lastPage: 1,
  perPage: 10,
  to: 1,
  from: 1,
  path: '',
  total: 0
})

export const emptyPagination = <T>(): PaginatableRecord<T> => ({
  data: [],
  links: emptyLinks(),
  meta: emptyMeta()
})
