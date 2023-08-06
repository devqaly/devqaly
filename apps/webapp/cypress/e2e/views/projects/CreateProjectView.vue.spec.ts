describe('CreateProjectView.vue', () => {
  let companyId: any

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id
    })
  })

  it('should allow to create correct project', () => {
    cy.intercept('POST', `api/companies/${companyId}/projects`).as('createProjectRequest')

    cy.visit('/projects/create')

    const projectName = 'project super awesome ' + Math.random().toString().slice(2, 8)

    cy.dataCy('create-project-view__title').type(projectName)

    cy.dataCy('create-project-view__submit').click()

    cy.wait('@createProjectRequest').then(({ request, response }) => {
      expect(request.body.title).to.be.eq(projectName)

      expect(response?.statusCode).to.be.eq(201)
      expect(response?.body.data.title).to.be.eq(projectName)
      expect(response?.body.data.projectKey).to.be.an('string')

      const projectId = response?.body.data.id

      expect(projectId).to.be.an('string')

      cy.url().should('include', `projects/${projectId}/dashboard`)
    })
  })
})
