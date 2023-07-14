import type { NetworkRequestEvent } from '@/services/api/resources/session/events/codec'

export function fromHeaderToText(
  headers:
    | NetworkRequestEvent['event']['requestHeaders']
    | NetworkRequestEvent['event']['responseHeaders']
): string {
  if (headers === null) {
    return 'No Headers'
  }

  return Object.keys(headers).reduce((_headers, headerName, i) => {
    if (i > 0) {
      return `${_headers}\n${headerName}: ${headers[headerName]}`
    }

    return `${headerName}: ${headers[headerName]}`
  }, '')
}
