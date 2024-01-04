describe('OnboardingCreateSessionView.vue.spec.ts', () => {
  let companyId: string
  let loggedUser: any
  let project: any

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id
      loggedUser = user

      cy.create({
        model: 'App\\Models\\Project\\Project',
        attributes: { company_id: companyId }
      }).then((_project) => {
        project = _project
      })
    })
  })

  context('no sessions ready', () => {
    it('should fetch sessions every 10 seconds', () => {
      cy.intercept('GET', `**/projects/${project.id}/sessions**`).as('fetchSessions')

      cy.clock()

      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

      cy.dataCy('onboarding-session-page__helper-record-session')

      cy.tick(10000)

      cy.wait('@fetchSessions').its('response.statusCode').should('equal', 200)

      cy.tick(10000)

      cy.wait('@fetchSessions').its('response.statusCode').should('equal', 200)
    })

    it('should show helper screen when there are no sessions available', () => {
      cy.clock()

      cy.intercept('GET', `**/projects/${project.id}/sessions**`).as('fetchSessions')

      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

      cy.dataCy('onboarding-session-page__helper-record-session').should('be.visible')

      cy.tick(10000)

      cy.wait('@fetchSessions').its('response.statusCode').should('equal', 200)

      cy.dataCy('onboarding-session-page__helper-record-session').should('be.visible')
    })
  })

  context('with sessions available', () => {
    let session: any

    beforeEach(() => {
      cy.create({
        model: 'App\\Models\\Session\\Session',
        attributes: { project_id: project.id, created_by_id: loggedUser.id },
        state: ['convertedVideoStatus', 'withOneEventForEachType'],
        load: ['events.eventable']
      }).then((s) => (session = s))
    })

    it('should list sessions when an available session is ready', () => {
      cy.clock()

      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

      cy.dataCy('onboarding-session-page__helper-record-session').should('be.visible')

      cy.tick(10000)

      cy.dataCy('onboarding-session-page__session-row')
        .should('have.length', 1)
        .should('have.attr', 'data-session-id')
        .and('equal', session.id)
    })

    it('should open dialog to view session', () => {
      cy.intercept('GET', `**/sessions/**/video`, {
        fixture: 'test-video.webm,null'
      }).as('videoRequest')

      cy.clock(new Date(), ['setInterval'])

      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

      cy.dataCy('onboarding-session-page__helper-record-session').should('be.visible')

      cy.tick(10000)

      cy.dataCy('onboarding-session-page__session-row').click()
      cy.dataCy('onboarding-session-page__see-session-dialog').should('be.visible')
    })
  })

  it('should display correct link for docs', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

    cy.dataCy('onboarding-session-page__see-docs')
      .should('have.attr', 'href')
      .and('contain', 'https://docs.devqaly.com/getting-started/introduction')

    cy.dataCy('onboarding-session-page__see-docs').should('have.attr', 'target').and('eq', '_blank')
  })

  it('should display correct link to next step', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/createSession`)

    cy.dataCy('onboarding-session-page__next-step')
      .should('have.attr', 'href')
      .and('contain', `onboarding/company/${companyId}/project/${project.id}/inviteTeamMembers`)
  })
})
