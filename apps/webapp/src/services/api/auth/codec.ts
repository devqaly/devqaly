export type LoginBody = { email: string; password: string; tokenName: string }

export type LoginResponseBody = {
  token: {
    plainTextToken: string
    accessToken: { name: string; abilities: string[] }
  }
}

export type RequestPasswordLinkBody = { email: string }

export type ResetPasswordBody = { token: string; email: string; password: string }
