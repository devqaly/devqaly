describe('ProjectSettingsView.vue', () => {
  let loggedUser: any
  let project: any
  let companyId: string

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id
      loggedUser = user

      cy.create({
        model: 'App\\Models\\Project\\Project',
        attributes: { company_id: companyId, created_by_id: loggedUser.id }
      }).then((_projects) => {
        project = _projects
      })
    })
  })

  context('general settings', () => {
    it('should display correct project name', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__project-title').should('have.value', project.title)
    })

    it('should display correct project key', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__project-key').should('have.value', project.project_key)
    })

    it('should have link to docs on `project key` section', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__read-docs-link')
        .should('have.attr', 'href')
        .and('contain', 'https://docs.devqaly.com')
    })

    it("should allow user to copy project's key", () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__copy-project-key').click()

      cy.contains('.p-toast-detail', 'Successfully copied project key to clipboard')

      cy.assertValueCopiedToClipboard(project.project_key)
    })
  })

  context('client security', () => {
    it('should display warning saying security token is private', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__security-token--warning').should('be.visible')
    })

    it('should display correct information for security token', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__security-token')
        .should('be.visible')
        .and('have.value', project.security_token)
    })

    it('should allow to copy security token', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__copy-security-token').click()

      cy.contains('.p-toast-detail', 'Successfully copied security token to clipboard')

      cy.assertValueCopiedToClipboard(project.project_key)
    })

    it('should allow to refresh security token', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')
      cy.intercept('PUT', `**/api/projects/${project.id}/securityToken`).as('updateSecurityToken')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__refresh-security-token').click()

      cy.contains('Yes, revoke current security token').click()

      cy.wait('@updateSecurityToken').then(({ response }) => {
        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data.securityToken).not.to.be.eq(project.security_token)

        cy.dataCy('project-settings-page__security-token')
          .should('be.visible')
          .and('not.have.value', project.security_token)
      })
    })

    it('should allow user to cancel updating security token', () => {
      cy.intercept('GET', `**/api/projects/${project.id}`).as('fetchProject')

      cy.visit(`company/${companyId}/projects/${project.id}/settings`)

      cy.wait('@fetchProject')

      cy.dataCy('project-settings-page__refresh-security-token').click()

      cy.contains('.p-dialog .p-dialog-footer .p-button-label', 'No').click()

      cy.get('.p-dialog').should('not.exist')
    })
  })
})
