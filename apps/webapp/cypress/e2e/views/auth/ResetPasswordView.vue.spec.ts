function validateFormAndFill({ email, newPassword }: { email: string; newPassword: string }) {
  cy.dataCy('reset-password-view__email').should('have.value', email)
  cy.dataCy('reset-password-view__new-password').type(newPassword)
}

describe('ResetPasswordView.vue', () => {
  before(() => {
    cy.refreshDatabase()
  })

  it('Should allow user to reset password', () => {
    cy.intercept('POST', '**/resetPassword', {
      statusCode: 200
    }).as('passwordResetRequest')

    const email = 'foo@devqaly.com'
    const token = 'randomtoken' + Math.random().toString().slice(2)
    const newPassword = 'newPassword' + Math.random().toString().slice(2)

    cy.visit(`/auth/resetPassword/${token}?email=${email}`)

    validateFormAndFill({ email, newPassword })

    cy.dataCy('reset-password-view__submit').click()

    cy.wait('@passwordResetRequest')

    cy.url().should('contain', '/auth/login')
  })

  it('Should show warning when token is invalid', () => {
    cy.intercept('POST', '**/resetPassword', {
      statusCode: 400
    }).as('passwordResetRequest')

    const email = 'foo@devqaly.com'
    const token = 'randomtoken' + Math.random().toString().slice(2)
    const newPassword = 'newPassword' + Math.random().toString().slice(2)

    cy.visit(`/auth/resetPassword/${token}?email=${email}`)

    validateFormAndFill({ email, newPassword })

    cy.dataCy('reset-password-view__submit').click()

    cy.wait('@passwordResetRequest')

    cy.contains('.p-toast-message', 'Token might have expired. Request a new password link')
  })
})
