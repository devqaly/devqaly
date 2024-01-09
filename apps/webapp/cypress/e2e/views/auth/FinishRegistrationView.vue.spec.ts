function fillForm({
  firstName,
  lastName,
  password
}: {
  firstName: string
  lastName: string
  password: string
}) {
  cy.dataCy('finish-registration__first-name').type(firstName)
  cy.dataCy('finish-registration__last-name').type(lastName)
  cy.dataCy('finish-registration__password').type(password)
  cy.dataCy('finish-registration__current-position').click()
  cy.contains('.p-dropdown-item', 'Developer').click()
}

describe('LoginView.vue.spec.ts', () => {
  let validRegisterToken: any
  let expiredRegisterToken: any
  let hasOnboardingRegisterToken: any

  const registeredEmail = 'fake-email@devqaly.com'

  before(() => {
    cy.refreshDatabase()
    cy.seed('DatabaseSeederCypress')

    cy.create({
      model: 'App\\Models\\Auth\\RegisterToken',
      attributes: { email: registeredEmail },
      state: ['unrevoked', 'withCompanyMember']
    }).then((token) => (validRegisterToken = token))

    cy.create({
      model: 'App\\Models\\Auth\\RegisterToken',
      state: ['unrevoked', 'expired']
    }).then((token) => (expiredRegisterToken = token))

    cy.create({
      model: 'App\\Models\\Auth\\RegisterToken',
      attributes: { email: 'has-onboarding@company.com' },
      state: ['unrevoked', 'hasOnboarding']
    }).then((token) => (hasOnboardingRegisterToken = token))
  })

  it('should allow user to finish registration with valid token', () => {
    cy.intercept('PUT', '**/registerTokens/**').as('finishRegistrationRequest')
    cy.intercept('POST', '**/login').as('loginRequest')
    cy.intercept('GET', '**/users/me').as('userInformationRequest')
    cy.intercept('GET', '**/companies**').as('fetchUserCompanies')

    const firstName = 'Bruno'
    const lastName = 'Francisco'
    const password = 'password123'
    const currentPosition = 'developer'

    cy.visit(`/auth/finishRegistration/${validRegisterToken.token}`)

    fillForm({ firstName, lastName, password })

    cy.dataCy('finish-registration__submit').click()

    cy.wait('@finishRegistrationRequest').then(({ request, response }) => {
      expect(request.body.firstName).to.be.eq(firstName)
      expect(request.body.lastName).to.be.eq(lastName)
      expect(request.body.password).to.be.eq(password)
      expect(request.body.currentPosition).to.be.eq(currentPosition)
      expect(request.body).to.haveOwnProperty('timezone')

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.data.user.email).to.be.eq(registeredEmail)
    })

    cy.url().should('contain', '/projects')

    cy.wait('@loginRequest').then(({ request, response }) => {
      expect(request.body.email).to.be.eq(registeredEmail)
      expect(request.body.password).to.be.eq(password)

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body).to.haveOwnProperty('token')
      expect(response?.body.token).to.haveOwnProperty('plainTextToken')
    })

    cy.wait('@userInformationRequest').then(({ response }) => {
      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.data.firstName).to.be.eq(firstName)
      expect(response?.body.data.lastName).to.be.eq(lastName)
      expect(response?.body.data).to.haveOwnProperty('timezone')
    })

    cy.wait('@fetchUserCompanies').then(({ response }) => {
      expect(response?.body.data).to.be.an('array')
      expect(response?.body.data).to.have.length(
        1,
        'There should be only a single company assigned to this user'
      )
    })
  })

  it('Should re-send an email to user when he tries to use an expired token', () => {
    cy.intercept('PUT', '**/registerTokens/**').as('finishRegistrationRequest')

    const firstName = 'Bruno'
    const lastName = 'Francisco'
    const password = 'password123'

    cy.visit(`/auth/finishRegistration/${expiredRegisterToken.token}`)

    fillForm({ firstName, lastName, password })

    cy.dataCy('finish-registration__submit').click()

    cy.wait('@finishRegistrationRequest').then(({ response }) => {
      expect(response?.body.message).to.be.eq(
        'Current token have expired. We have sent a new token to the email associated with this token'
      )

      expect(response?.statusCode).to.be.eq(403)

      cy.contains(
        '.p-toast-message',
        'Current token have expired. We have sent a new token to the email associated with this token'
      )

      cy.url().should('contain', 'auth/finishRegistration')
    })
  })

  it('should redirect user to onboarding when response has `data.registerToken.hasOnboarding` set to true', () => {
    cy.intercept('PUT', '**/registerTokens/**').as('finishRegistrationRequest')

    const firstName = 'Bruno'
    const lastName = 'Francisco'
    const password = 'password123'

    cy.visit(`/auth/finishRegistration/${hasOnboardingRegisterToken.token}`)

    fillForm({ firstName, lastName, password })

    cy.dataCy('finish-registration__submit').click()

    cy.wait('@finishRegistrationRequest').then(({ response }) => {
      expect(response?.body.data.registerToken.hasOnboarding).to.be.eq(true)

      expect(response?.statusCode).to.be.eq(200)

      const companyId = response?.body.data.company.id
      const projectId = response?.body.data.project.id

      cy.url().should('contain', `onboarding/company/${companyId}/project/${projectId}/installing`)
    })
  })
})
