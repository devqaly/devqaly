import type { CompanyCodec } from '@/services/api/resources/company/codec'

export const SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME = 'showFreeTrialInfo'

export function assertsIsCompanyCodec(
  company: CompanyCodec | null
): asserts company is CompanyCodec {
  if (company === null) throw new Error('`company` should not be null')
}

export function hasBlockedReasons(reasons: CompanyCodec['blockedReasons']): boolean {
  if (reasons === null) return false

  if (reasons === undefined) return false

  return reasons.length > 0
}
