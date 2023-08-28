import type { WrappedResponse } from '@/services/api/axios'
import { app } from '@/main'
import type { ToastMessageOptions } from 'primevue/toast'
import type { Links, Meta } from '@/services/api'
import type { PaginatorProps } from 'primevue/paginator'

export const displayGeneralError = (
  error: WrappedResponse,
  toastOptions: Partial<ToastMessageOptions> = {}
) => {
  if (!error.response) return

  app.config.globalProperties.$toast.add({
    severity: 'error',
    summary: 'An error occurred',
    detail: error.response.data.message,
    life: 3000,
    ...toastOptions
  })
}

export const getPaginationPropsForMeta = (meta: Meta, perPage: number): PaginatorProps => ({
  totalRecords: meta.total,
  rows: perPage,
  first: (meta.currentPage - 1) * perPage
})

export const hasNextPage = (links: Links): boolean => {
  return !!links.next
}

export const copyToClipboard = (text: string): Promise<void> => {
  return navigator.clipboard.writeText(text)
}
