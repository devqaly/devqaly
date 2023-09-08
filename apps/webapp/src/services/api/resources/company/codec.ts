import type { ResourceID } from '@/services/api'
import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'

export interface CompanyCodec {
  id: ResourceID
  name: string
  subscription: SubscriptionCodec | null
}
