import platform from 'platform'

export const systemTimezone = () => Intl.DateTimeFormat().resolvedOptions().timeZone

export const createTokenName = (): string => {
  return `${platform.name} (${platform.os})`
}
