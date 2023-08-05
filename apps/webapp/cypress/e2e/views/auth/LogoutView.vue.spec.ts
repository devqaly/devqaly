describe('LoginView.vue.spec.ts', () => {
  before(() => {
    cy.refreshDatabase()
    cy.seed('DatabaseSeederCypress')

    cy.login()
  })

  it('Should logout user correctly', () => {
    cy.intercept('POST', '**/logout').as('logoutRequest')

    cy.visit('/auth/logout')

    cy.wait('@logoutRequest').then(({ response }) => {
      expect(response?.statusCode).to.be.eq(204)

      cy.contains('.p-toast-message', 'You have been successfully logged out')

      expect(window.localStorage.getItem('_token')).to.be.null

      cy.url().should('contain', 'auth/login')
    })
  })
})
