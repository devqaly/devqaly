describe('CreateCompanyView.vue', () => {
  let companyId: string
  let loggedUser: any

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id
      loggedUser = user
    })
  })

  context('payment method', () => {
    it('should show warning when no payment method is added', () => {
      cy.visit(`/company/${companyId}/subscription`)

      cy.dataCy('company-subscription-view__no-payment-method-added-warning').should('be.visible')
    })

    it('should have link to stripe portal on payment method', () => {
      cy.visit(`/company/${companyId}/subscription`)

      cy.dataCy('company-subscription-view__stripe-portal-url')
        .should('have.attr', 'href')
        .and('contain', 'https://billing.stripe.com/p/session/test_')
    })
  })

  context('billing contact', () => {
    it('should allow to update billing contact', () => {
      cy.intercept('PUT', '**/billingDetails').as('updateBillingDetails')

      const billingContact = 'bruno.francisco@devqaly.com'

      cy.visit(`/company/${companyId}/subscription`)

      cy.dataCy('company-subscription-view__billing-contact').should(
        'have.text',
        'No Billing Contact'
      )

      cy.dataCy('company-subscription-view__open-billing-contact-dialog').click()

      cy.dataCy('company-subscription-view__billing-contact-input').type(billingContact)

      cy.dataCy('company-subscription-view__billing-contact-submit').click()

      cy.wait('@updateBillingDetails').then(({ request, response }) => {
        expect(request.body.billingContact).to.be.eq(billingContact)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data.billingContact).to.be.eq(billingContact)
      })

      cy.dataCy('company-subscription-view__billing-contact').should('have.text', billingContact)
    })

    it('should close modal when clicking cancel', () => {
      cy.visit(`/company/${companyId}/subscription`)

      cy.dataCy('company-subscription-view__open-billing-contact-dialog').click()

      cy.contains('button', 'Cancel').click()

      cy.dataCy('company-subscription-view__billing-contact-input').should('not.exist')
    })
  })
})
