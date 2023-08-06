import type { SessionCodec } from '../../../../src/services/api/resources/session/codec'

describe('ProjectSessionsView.vue', () => {
  const numberSessions = 60
  const perPage = 20

  let project: any
  let companyId: string
  let loggedUser: any
  let sessions: any[]

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
      }).then((_projects) => {
        project = _projects

        cy.create({
          model: 'App\\Models\\Session\\Session',
          attributes: { project_id: project.id, created_by_id: loggedUser.id },
          count: numberSessions
        }).then((_sessions) => (sessions = _sessions))
      })
    })
  })

  it('should display correct number sessions', () => {
    cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

    cy.visit(`projects/${project.id}/sessions`)

    cy.wait('@fetchSessionRequest').then(({ request, response }) => {
      expect(request.url).to.include('createdAtOrder=desc')
      expect(request.url).to.include('page=1')
      expect(request.url).to.include(`perPage=${perPage}`)

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.data).to.be.an('array')
      expect(response?.body.data).to.be.length(perPage)
      expect(response?.body.meta.total).to.be.eq(numberSessions)

      const sessions: SessionCodec[] = response?.body.data

      sessions.forEach((session) => {
        cy.get(`[data-cy="project-sessions__see-session"][data-session-id="${session.id}"]`)
          .should('have.attr', 'href')
          .and('contain', `sessions/${session.id}`)
      })
    })

    cy.get('.p-datatable-tbody tr').should('have.length', perPage)
  })

  context('filters', () => {
    it('should make request when searching by created by', () => {
      cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

      const searchCreatedBy = loggedUser.first_name

      cy.visit(`projects/${project.id}/sessions`)

      cy.wait('@fetchSessionRequest')

      cy.dataCy('project-sessions-view__created-by-filter').type(searchCreatedBy)

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.include(`createdByName=${encodeURIComponent(searchCreatedBy)}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.data).to.be.length(perPage)

        const results = response?.body.data

        cy.get('.p-datatable-tbody tr').should('have.length', results.length)
      })
    })

    it('should make request when searching by OS', () => {
      cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

      const os = sessions[0].os
      const sessionsWithOS = sessions.filter((s) => s.os === os)

      cy.visit(`projects/${project.id}/sessions`)

      cy.wait('@fetchSessionRequest')

      cy.dataCy('project-sessions-view__os-filter').type(os)

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.include(`os=${encodeURIComponent(os)}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.meta.total).to.be.eq(sessionsWithOS.length)

        const results = response?.body.data

        cy.get('.p-datatable-tbody tr').should('have.length', results.length)
      })
    })

    it('should make request when searching by platform', () => {
      cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

      const platform = sessions[0].platform_name
      const sessionsWithPlatform = sessions.filter((s) => s.platform_name === platform)

      cy.visit(`projects/${project.id}/sessions`)

      cy.wait('@fetchSessionRequest')

      cy.dataCy('project-sessions-view__platform-filter').type(platform)

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.include(`platform=${encodeURIComponent(platform)}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.meta.total).to.be.eq(sessionsWithPlatform.length)

        const results = response?.body.data

        cy.get('.p-datatable-tbody tr').should('have.length', results.length)
      })
    })

    it('should make request when searching by version', () => {
      cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

      const version = sessions[0].version
      const sessionsWithVersion = sessions.filter((s) => s.version === version)

      cy.visit(`projects/${project.id}/sessions`)

      cy.wait('@fetchSessionRequest')

      cy.dataCy('project-sessions-view__version-filter').type(version)

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.include(`version=${encodeURIComponent(version)}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.meta.total).to.be.eq(sessionsWithVersion.length)

        const results = response?.body.data

        cy.get('.p-datatable-tbody tr').should('have.length', results.length)
      })
    })
  })

  context('pagination', () => {
    it('should make request when changing pages', () => {
      cy.intercept('GET', `**/api/projects/${project.id}/sessions**`).as('fetchSessionRequest')

      cy.visit(`projects/${project.id}/sessions`)

      cy.wait('@fetchSessionRequest')

      cy.get('.p-paginator-page[aria-label="2"]').click()

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.contain('page=2')
        expect(request.url).to.contain(`perPage=${perPage}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.meta.total).to.be.eq(numberSessions)
        expect(response?.body.meta.current_page).to.be.eq(2)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.data).to.have.length(perPage)
      })

      cy.get('.p-paginator-page[aria-label="3"]').click()

      cy.wait('@fetchSessionRequest').then(({ request, response }) => {
        expect(request.url).to.contain('page=3')
        expect(request.url).to.contain(`perPage=${perPage}`)

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.meta.total).to.be.eq(numberSessions)
        expect(response?.body.meta.current_page).to.be.eq(3)
        expect(response?.body.data).to.be.an('array')

        // We need to subtract the number of items that already appeared in the first 2 pages
        expect(response?.body.data).to.have.length(numberSessions - perPage * 2)
      })
    })
  })
})
