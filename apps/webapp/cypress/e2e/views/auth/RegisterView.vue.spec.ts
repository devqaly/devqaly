describe('LoginView.vue.spec.ts', () => {
  before(() => {
    cy.refreshDatabase()
  })

  it('should show error when invalid email is entered', () => {
    const invalidEmail = 'invalid-email-at-devqaly'

    cy.visit('/auth/register')

    cy.dataCy('register-view__email').type(invalidEmail)
    cy.dataCy('register-view__submit').click()
    cy.contains('.p-error', 'Invalid email')
    cy.dataCy('register-view__resend-email-container').should('not.exist')
  })

  it('should make request when sending entered correct email', () => {
    cy.intercept('POST', '**/registerTokens').as('createRegisterToken')

    const validEmail = 'correct-email@devqaly.com'

    cy.visit('/auth/register')

    cy.dataCy('register-view__email').type(validEmail)
    cy.dataCy('register-view__submit').click()

    cy.wait('@createRegisterToken').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(validEmail)
      expect(response?.statusCode).to.be.eq(204)
    })

    cy.dataCy('register-view__resend-email-container').should('be.visible')
  })

  it('should show error when email already exists', () => {
    cy.intercept('POST', '**/registerTokens').as('createRegisterToken')

    const alreadyCreatedEmail = 'bruno.francisco@devqaly.com'

    cy.create({
      model: 'App\\Models\\User',
      attributes: { email: alreadyCreatedEmail }
    }).then(() => {
      cy.visit('/auth/register')

      cy.dataCy('register-view__email').type(alreadyCreatedEmail)
      cy.dataCy('register-view__submit').click()

      cy.wait('@createRegisterToken').then(({ request, response }) => {
        expect(request.body.email).to.be.eq(alreadyCreatedEmail)
        expect(response?.statusCode).to.be.eq(422)
        expect(response?.body.message).to.be.eq('The email has already been taken.')
      })

      cy.contains('.p-error', 'The email has already been taken.')
    })
  })

  it('should allow user to re-send email after signing up', () => {
    cy.clock(new Date(), ['setTimeout'])

    cy.intercept('POST', '**/registerTokens').as('createRegisterToken')
    cy.intercept('POST', '**/registerTokens/resendEmail').as('resendEmail')

    const email = `bruno.francisco.${Math.random().toString().slice(2, 5)}@devqaly.com`

    cy.visit('/auth/register')

    cy.dataCy('register-view__email').type(email)
    cy.dataCy('register-view__submit').click()

    // For some reason we need to tick here to trigger the submission of the form
    cy.tick(100)

    cy.wait('@createRegisterToken')

    cy.dataCy('register-view__resend-email').click()

    cy.wait('@resendEmail').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(email)
      expect(response?.statusCode).to.be.eq(204)
    })

    cy.dataCy('register-view__resend-email').should('be.disabled')

    cy.tick(60000)

    cy.dataCy('register-view__resend-email').should('not.be.disabled')

    cy.dataCy('register-view__resend-email').click()

    cy.wait('@resendEmail').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(email)
      expect(response?.statusCode).to.be.eq(204)
    })
  })
})
