import type { ProjectCodec } from '../../../../src/services/api/resources/project/codec'

describe('ListProjectsView.vue', () => {
  let companyId: string
  let projects: any[]
  const numberProjects = 60
  const perPage = 50

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id

      cy.create({
        model: 'App\\Models\\Project\\Project',
        attributes: { company_id: companyId },
        count: numberProjects
      }).then((_projects) => (projects = _projects))
    })
  })

  it('should display correct projects', () => {
    cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')

    cy.visit(`company/${companyId}/projects`)

    cy.wait('@projectsRequest').then(({ request, response }) => {
      expect(request.url).to.contain(`perPage=${perPage}`)

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.data).to.be.an('array')
      expect(response?.body.data).to.have.length(perPage)
      expect(response?.body.meta.total).to.be.eq(numberProjects)

      const _projects: ProjectCodec[] = response?.body.data

      _projects.forEach((project) => {
        cy.get(`[data-cy="list-projects-view__project-dashboard"][data-project-id="${project.id}"]`)
          .should('have.attr', 'href')
          .and('contain', `projects/${project.id}/dashboard`)

        cy.get(`[data-cy="list-projects-view__project-sessions"][data-project-id="${project.id}"]`)
          .should('have.attr', 'href')
          .and('contain', `projects/${project.id}/sessions`)

        cy.get(
          `[data-cy="list-projects-view__project-integration-details"][data-project-id="${project.id}"]`
        ).should('exist')
      })
    })

    cy.get('.p-datatable-tbody tr').should('have.length', perPage)
  })

  it('should open modal when clicking to view integration details', () => {
    cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')

    cy.visit(`company/${companyId}/projects`)

    cy.wait('@projectsRequest')

    cy.dataCy('list-projects-view__project-integration-details').first().click()

    cy.dataCy('setup-sdk-dialog').should('be.visible')
  })

  context('filters', () => {
    it('should make request when searching by title', () => {
      cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')

      const searchTitle = projects[0].title

      cy.visit(`company/${companyId}/projects`)

      cy.wait('@projectsRequest')

      cy.dataCy('list-projects-view__title-filter').type(searchTitle)

      cy.wait('@projectsRequest').then(({ request, response }) => {
        expect(request.url).to.contain(`title=${encodeURIComponent(searchTitle)}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')

        const numberProjects = response?.body.data.length

        cy.get('.p-datatable-tbody tr').should('have.length', numberProjects)
        cy.get('.p-datatable-tbody tr').should('contain', projects[0].title)
      })
    })
  })

  context('pagination', () => {
    it('should display correct values when jumping to next page', () => {
      cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')

      cy.visit(`company/${companyId}/projects`)

      cy.wait('@projectsRequest')

      cy.get('.p-paginator-page[aria-label="2"]').click()

      cy.wait('@projectsRequest').then(({ request, response }) => {
        expect(request.url).to.contain('page=2')
        expect(request.url).to.contain(`perPage=${perPage}`)

        expect(response?.statusCode).to.be.eq(200)
        // We need to account for the owner of the company
        expect(response?.body.meta.total).to.be.eq(numberProjects)
        expect(response?.body.meta.current_page).to.be.eq(2)
        expect(response?.body.data).to.be.an('array')
        // We need to account for the owner of the company
        expect(response?.body.data).to.have.length(numberProjects - perPage)
      })
    })
  })

  context('deletion', () => {
    it('should close modal for deletion when clicking cancel', () => {
      const project = projects[0]

      cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')

      cy.visit(`company/${companyId}/projects`)

      cy.wait('@projectsRequest')

      cy.dataCy('list-projects-view__delete-project', {
        'data-project-id': project.id
      }).click()

      cy.dataCy('list-projects-view__delete-project-dialog-project-name').should(
        'have.text',
        project.title
      )

      cy.dataCy('list-projects-view__cancel-delete-project-dialog').click()

      cy.dataCy('list-projects-view__delete-project-dialog').should('not.exist')
    })

    it('should delete project and remove from DOM', () => {
      const project = projects[0]

      cy.intercept('GET', `**/companies/${companyId}/projects**`).as('projectsRequest')
      cy.intercept('DELETE', `**/projects/${project.id}`).as('deleteProject')

      cy.visit(`company/${companyId}/projects`)

      cy.wait('@projectsRequest')

      cy.dataCy('list-projects-view__delete-project', {
        'data-project-id': project.id
      }).click()

      cy.dataCy('list-projects-view__confirm-delete-project').click()

      cy.wait('@deleteProject').then(({ response }) => {
        expect(response?.statusCode).to.be.eq(204)
      })

      cy.dataCy('list-projects-view__delete-project-dialog').should('not.exist')

      cy.dataCy('list-projects-view__delete-project', {
        'data-project-id': project.id
      }).should('not.exist')
    })
  })
})
