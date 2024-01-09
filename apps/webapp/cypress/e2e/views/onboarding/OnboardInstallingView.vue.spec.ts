describe('OnboardInstallingView.vue.spec.ts', () => {
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

  it('should have the correct project name', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/installing`)

    cy.dataCy('onboarding-installing-page__project-name').should('have.text', project.title)
  })

  it('should have the correct link to docs', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/installing`)

    cy.dataCy('onboarding-installing-page__see-docs')
      .should('have.attr', 'href')
      .and('contain', 'https://docs.devqaly.com/getting-started/introduction')

    cy.dataCy('onboarding-installing-page__see-docs')
      .should('have.attr', 'target')
      .and('eq', '_blank')
  })

  it('should have the projectKey listed in the SDK', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/installing`)

    cy.dataCy('initiate-devqaly-script').should('contain', project.project_key)
  })

  it('should have correct link for next step', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/installing`)

    cy.dataCy('onboarding-installing-page__next-step')
      .should('have.attr', 'href')
      .and('contain', `/onboarding/company/${companyId}/project/${project.id}/createSession`)
  })

  it('should allow to skip step', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/installing`)

    cy.dataCy('onboarding-installing-page__skip-step').click()

    cy.url().should('contain', `company/${companyId}/project/${project.id}/createSession`)
  })
})
