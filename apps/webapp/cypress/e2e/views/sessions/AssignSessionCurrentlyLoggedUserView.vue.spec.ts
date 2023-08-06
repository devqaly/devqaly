describe('AssignSessionCurrentlyLoggedUserView.vue', () => {
  let loggedUser: any
  let companyId: string
  let project: any
  let session: any

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

        cy.create({
          model: 'App\\Models\\Session\\Session',
          attributes: { project_id: project.id },
          state: ['unassigned']
        }).then((_session) => (session = _session))
      })
    })
  })

  it('should allow user to assign session to itself', () => {
    cy.intercept('POST', `**/sessions/${session.id}/assign`).as('assignSession')

    cy.visit(`/sessions/${session.id}/assign`)

    cy.contains('Assigning session to your account')

    cy.wait('@assignSession').then(({ request, response }) => {
      expect(request.body.userId).to.be.eq(loggedUser.id)

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.data.id).to.be.eq(session.id)
    })

    cy.url().should('contain', `projects/${project.id}/sessions/${session.id}`)
  })

  it('should redirect user even if request fails and show warning', () => {
    cy.intercept('POST', `**/sessions/${session.id}/assign`, {
      statusCode: 400
    }).as('assignSession')

    cy.visit(`/sessions/${session.id}/assign`)

    cy.contains('Assigning session to your account')

    cy.wait('@assignSession')

    cy.contains(
      '.p-toast-message',
      'There was an error assigning this session to you. We will redirect you 10 seconds and this session will be unassigned'
    )
  })
})
