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

    cy.visit(`projects/${project.id}/dashboard`)

    cy.wait('@getProjectRequest')

    cy.contains('npm install @devqaly/browser')
    cy.contains('yarn add @devqaly/browser')
    cy.contains('Then you will have to initiate the SDK')
    cy.contains("import { DevqalySDK } from '@devqaly/browser'")
    cy.contains('const devqaly = new DevqalySDK({')
    cy.contains(`projectKey: '${project.project_key}'`)
    cy.contains('devqaly.showRecordingButton()')
  })
})
