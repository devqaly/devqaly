export type CreateRegisterTokenBody = { email: string }

export type ResendEmailRegisterTokenBody = { email: string }

export type UpdateRegisterTokenBody = {
  firstName: string
  lastName: string
  password: string
  timezone: string
  currentPosition: string
}

export type UpdateRegisterTokenResponseBody = {
  user: { email: string }
  company?: { id: string | null }
  project?: { id: string | null }
  registerToken: { hasOnboarding: boolean }
}
