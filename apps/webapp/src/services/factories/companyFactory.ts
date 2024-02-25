import type { CompanyCodec } from '@/services/api/resources/company/codec'
import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'

export const companyFactoryCodec = (): CompanyCodec => ({
  id: '',
  name: '',
  trialEndsAt: null,
  paymentMethodType: null,
  paymentLastFourDigits: null,
  subscription: null
})

export const subscriptionFactoryCodec = (): SubscriptionCodec => ({
  createdAt: new Date().toUTCString(),
  endsAt: null,
  status: 'active',
  trialEndsAt: null,
  planName: 'gold'
})
