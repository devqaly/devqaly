import type { CompanyCodec } from '@/services/api/resources/company/codec'

export const companyFactoryCodec = (): CompanyCodec => ({
  id: '',
  name: '',
  trialEndsAt: null,
  paymentMethodType: null,
  paymentLastFourDigits: null
})
