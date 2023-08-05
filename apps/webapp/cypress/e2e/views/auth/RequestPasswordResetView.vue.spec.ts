describe('LoginView.vue.spec.ts', () => {
  before(() => {
    cy.refreshDatabase()
    cy.seed('DatabaseSeederCypress')
  })

  it('Should logout user correctly', () => {
    cy.intercept('POST', '**/resetPassword/link').as('passwordRequest')

    const email = 'foo@devqaly.com'

    cy.visit('/auth/requestPasswordLink')

    cy.dataCy('request-password-reset__email').type(email)

    cy.dataCy('request-password-reset__submit').click()

    cy.wait('@passwordRequest').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(email)

      expect(response?.statusCode).to.be.eq(204)
    })

    cy.dataCy('request-password-reset__link-requested').should('be.visible')
  })
})
