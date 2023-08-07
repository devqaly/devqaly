describe('ProjectSessionView.vue', () => {
  let loggedUser: any
  let project: any
  let companyId: string
  let sessionWithConvertedVideo: any
  let sessionWithInQueueForConvertingVideo: any

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

        cy.create({
          model: 'App\\Models\\Session\\Session',
          attributes: { project_id: project.id, created_by_id: loggedUser.id },
          state: ['convertedVideoStatus']
        }).then((_session) => (sessionWithConvertedVideo = _session))

        cy.create({
          model: 'App\\Models\\Session\\Session',
          attributes: { project_id: project.id, created_by_id: loggedUser.id },
          state: ['inQueueForConversionVideoStatus']
        }).then((_session) => (sessionWithInQueueForConvertingVideo = _session))
      })
    })
  })

  it('should display correct information for session with converted video', () => {
    cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}`).as('fetchSession')

    cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

    cy.wait('@fetchSession')

    cy.dataCy('project-session-view__os').should('have.text', sessionWithConvertedVideo.os)
    cy.dataCy('project-session-view__platform').should(
      'have.text',
      sessionWithConvertedVideo.platform_name
    )
    cy.dataCy('project-session-view__screen-size').should(
      'have.text',
      `${sessionWithConvertedVideo.window_width}x${sessionWithConvertedVideo.window_height}`
    )
    cy.dataCy('project-session-view__created-by').should(
      'have.text',
      `${loggedUser.first_name} ${loggedUser.last_name}`
    )
    cy.dataCy('project-session-view__video-status').should('have.text', 'Converted')

    cy.dataCy('project-session-view__video')
      .should('have.attr', 'src')
      .and('contain', `${sessionWithConvertedVideo.id}.webm`)

    cy.dataCy('project-session-view__live-preview-section').should('be.visible')
    cy.dataCy('list-event__event').should('have.length', 0)

    cy.dataCy('project-session-view__bottom-events-section').should('be.visible')
  })

  it('should display info when session is being converted', () => {
    cy.intercept('GET', `**/api/sessions/${sessionWithInQueueForConvertingVideo.id}`).as(
      'fetchSession'
    )

    cy.visit(`projects/${project.id}/sessions/${sessionWithInQueueForConvertingVideo.id}`)

    cy.wait('@fetchSession')

    cy.dataCy('project-session-view__video-being-converted-info').should('be.visible')

    cy.dataCy('project-session-view__bottom-events-section').should('not.exist')
  })

  context('events [bottom and live preview]', () => {
    it('should allow user to see events from a session', () => {})
    it('should show new events when user skips the video to X seconds', () => {})
    it('should open events details when clicking on button to view more details', () => {})
  })

  context('events [bottom tabs]', () => {
    it('should show events for the event type when clicking the tab', () => {})
  })
})
