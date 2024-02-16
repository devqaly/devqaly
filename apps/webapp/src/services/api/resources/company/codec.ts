import type { DateTime, ResourceID } from '@/services/api'
import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'

export interface CompanyCodec {
  id: ResourceID
  name: string
  trialEndsAt?: DateTime | null
  paymentMethodType?: string | null
  paymentLastFourDigits?: string | null
  subscription?: SubscriptionCodec | null
}

export interface CompanyStripePortalResponse {
  portalUrl: string
}
