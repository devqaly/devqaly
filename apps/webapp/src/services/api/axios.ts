import type { AxiosResponse } from 'axios'
import axiosBase, { HttpStatusCode } from 'axios'
import { stringify } from 'qs'

const serializeParameters = (params: Record<string, unknown>) => {
  const cleanedParameters = Object.keys(params).reduce((previous: Record<string, unknown>, key) => {
    const accumulator: Record<string, unknown> = { ...previous }
    const value = params[key]

    // Only accept strings that are not empty
    if (typeof value === 'string' && value.trim() === '') {
      return accumulator
    }

    // if (typeof value === "boolean") {
    //   value = value ? BOOLEAN_TRUE : BOOLEAN_FALSE;
    // }

    if (value === null) {
      return accumulator
    }

    accumulator[key] = value

    return accumulator
  }, {})

  return stringify(cleanedParameters, { arrayFormat: 'brackets' })
}

export type WrappedResponse = { response?: AxiosResponse }

export const hasInvalidFields = (e: WrappedResponse) =>
  isError(e, HttpStatusCode.UnprocessableEntity)

export const isError = (e: WrappedResponse, statusCode: number) =>
  e.response && e.response.status === statusCode

type UnconvertedMeta = {
  current_page?: number
  last_page?: number
  per_page?: number
}

const camelCaseMeta = (meta: UnconvertedMeta) => {
  const currentPage = meta.current_page
  const lastPage = meta.last_page
  const perPage = meta.per_page

  delete meta.current_page
  delete meta.last_page
  delete meta.per_page

  return {
    ...meta,
    currentPage,
    lastPage,
    perPage
  }
}

export const axios = axiosBase.create({
  baseURL: import.meta.env.VITE_BASE_API_URL,
  timeout: 10000,
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
  paramsSerializer: serializeParameters
})

axios.interceptors.response.use(
  function (response) {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data

    if (response.data.meta) {
      response.data.meta = camelCaseMeta(response.data.meta)
    }

    return response
  },
  function (error) {
    if (error.response) {
      if (error.response.status === HttpStatusCode.InternalServerError) {
        // The request has an internal error
      }
    } else if (error.request) {
      // The request was made but no response was received
      console.log(error.request)
    } else {
      // Something happened in setting up the request that triggered an Error
      console.log('Error', error.message)
    }

    return Promise.reject(error)
  }
)
