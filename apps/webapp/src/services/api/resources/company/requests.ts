export type CreateCompanyBody = { name: string }

export type GetCompanyStripePortalRequest = { returnUrl: string }

export type UpdateCompanyBillingDetailsBody = { billingContact?: string; invoiceDetails?: string }
