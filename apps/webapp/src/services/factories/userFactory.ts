import type { LoggedUserCodec } from '@/services/api/resources/user/codec'

export const loggedUserCodecFactory = (): LoggedUserCodec => ({
  id: '',
  firstName: '',
  lastName: '',
  timezone: '',
  fullName: ''
})
