import type { CompanyCodec } from '@/services/api/resources/company/codec'

export function assertsIsCompanyCodec(
  company: CompanyCodec | null
): asserts company is CompanyCodec {
  if (company === null) throw new Error('`company` should not be null')
}
