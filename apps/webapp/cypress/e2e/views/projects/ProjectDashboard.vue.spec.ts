describe('ProjectDashboard.vue', () => {
  let companyId: string
  let project: any

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id

      cy.create({
        model: 'App\\Models\\Project\\Project',
        attributes: { company_id: companyId }
      }).then((_projects) => (project = _projects))
    })
  })

  it('should show how to setup the SDK', () => {
    cy.intercept('GET', `**/api/projects/${project.id}`).as('getProjectRequest')

    cy.visit(`company/${companyId}/projects/${project.id}/dashboard`)

    cy.wait('@getProjectRequest')

    cy.dataCy('install-devqaly').should('be.visible')
    cy.dataCy('initiate-devqaly-script').should('be.visible')
    cy.contains(`projectKey: '${project.project_key}'`)
  })
})
