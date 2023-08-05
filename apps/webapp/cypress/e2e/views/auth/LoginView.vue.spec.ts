describe('LoginView.vue.spec.ts', () => {
  before(() => {
    cy.refreshDatabase()
    cy.seed('DatabaseSeederCypress')
  })

  it('assert user can login', () => {
    cy.intercept('POST', '**/login').as('loginRequest')

    cy.visit('/auth/login')

    cy.dataCy('login-view__email').type(Cypress.env('AUTH_EMAIL'))
    cy.dataCy('login-view__password').type(Cypress.env('AUTH_PASSWORD'))
    cy.dataCy('login-view__submit').click()

    cy.wait('@loginRequest').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(Cypress.env('AUTH_EMAIL'))
      expect(request.body.password).to.be.eq(Cypress.env('AUTH_PASSWORD'))
      expect(request.body.tokenName).to.not.be.null

      expect(response?.statusCode).to.be.eq(200)
    })

    cy.url().should('contain', '/projects')
  })

  it('should show message when login credentials are wrong', () => {
    cy.intercept('POST', '**/login').as('loginRequest')

    cy.visit('/auth/login')

    cy.dataCy('login-view__email').type('wrong@devqaly.com')
    cy.dataCy('login-view__password').type('wrongpassword')
    cy.dataCy('login-view__submit').click()

    cy.wait('@loginRequest').then(({ response }) => {
      expect(response?.statusCode).to.be.eq(403)
    })

    cy.contains('.p-toast-message', 'Invalid Credentials')
  })

  it('should have "Forgot your password" link', () => {
    cy.visit('/auth/login')

    cy.dataCy('login-view__reset-password')
      .should('have.attr', 'href')
      .and('contain', '/auth/requestPasswordLink')
  })
})
