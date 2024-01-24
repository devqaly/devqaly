import { describe, it, expect, vi } from 'vitest'
import type { SubscriptionCodec } from '@/services/api/resources/subscription/codec'
import {
  isActiveSubscription,
  shouldShowSubscriptionConcerns
} from '@/services/resources/Subscription'

describe('Subscription.ts', () => {
  it('returns true for active subscription status', () => {
    const activeStatus: SubscriptionCodec['status'] = 'active'
    const result = isActiveSubscription(activeStatus)

    expect(result).toBe(true)
  })

  it('returns false for non-active subscription status', () => {
    const inactiveStatus: SubscriptionCodec['status'] = 'canceled'
    const result = isActiveSubscription(inactiveStatus)

    expect(result).toBe(false)
  })

  it('returns true for cloud version', () => {
    vi.stubEnv('VITE_DEVQALY_IS_SELF_HOSTING', 'false')

    const result = shouldShowSubscriptionConcerns()

    expect(result).toBe(true)
  })

  it('returns false for self-hosted version', () => {
    vi.stubEnv('VITE_DEVQALY_IS_SELF_HOSTING', 'true')

    const result = shouldShowSubscriptionConcerns()

    expect(result).toBe(false)
  })
})
