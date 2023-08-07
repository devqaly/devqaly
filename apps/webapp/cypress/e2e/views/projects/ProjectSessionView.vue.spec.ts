function whenClickingTabEventsShowUp({
  tabIdentifier,
  eventType,
  events
}: {
  tabIdentifier: string
  eventType: string
  events: any[]
}) {
  cy.dataCy(tabIdentifier).click()

  cy.get(
    `[data-cy="project-session-view__bottom-events-section"] [data-cy="list-event__event"][data-event-type="${eventType}"]`
  )
    .should('have.length', events.length)
    .and('be.visible')
}

describe('ProjectSessionView.vue', () => {
  let loggedUser: any
  let project: any
  let companyId: string
  let sessionWithConvertedVideo: any
  let sessionWithInQueueForConvertingVideo: any

  const NUMBER_EVENT_TYPES = 7

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
          state: ['convertedVideoStatus', 'withOneEventForEachType'],
          load: ['events.eventable']
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
    it('should allow user to see events from a session', () => {
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}`).as('fetchSession')
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}/events**`).as(
        'fetchSessionEvents'
      )

      cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

      cy.wait('@fetchSession')

      // @TODO: [BUG] The first partition is being fetched in the past
      // E.g.: The video starts at 10:00:00, the first partition
      // is trying to get events created at 09:59:50
      cy.wait('@fetchSessionEvents')

      cy.wait('@fetchSessionEvents').then(({ request, response }) => {
        expect(request.url).to.include('endCreatedAt=')
        expect(request.url).to.include('startCreatedAt=')

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')

        // We are creating one event per event type in the
        // backend using the state "withOneEventForEachType".
        // They are created with a `client_utc_event_created_at` of
        // only a second after the video "starts".
        expect(response?.body.data).to.be.have.length(NUMBER_EVENT_TYPES)
      })

      // In the preview mode we should be able to see NUMBER_EVENT_TYPES events
      // Since we are creating one event of each type of event
      cy.dataCy('project-session-view__live-preview-section').within(() => {
        cy.dataCy('list-event__event').should('have.length', NUMBER_EVENT_TYPES)
      })
    })

    it('should show new events when user skips the video to X seconds', () => {
      // TBD
    })

    it('should open events details when clicking on button to view more details', () => {
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}`).as('fetchSession')
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}/events**`).as(
        'fetchSessionEvents'
      )

      cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

      cy.wait(['@fetchSession', '@fetchSessionEvents'])

      const eventLog = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventLog'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventLog.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--log-source').should('contain', eventLog.source)

      cy.dataCy('project-session-view__active-event--log-level').should(
        'contain',
        eventLog.eventable.level
      )

      cy.dataCy('project-session-view__active-event--log-log').should(
        'contain',
        eventLog.eventable.log
      )

      const eventDatabaseTransaction = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventDatabaseTransaction'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventDatabaseTransaction.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--db-source').should(
        'contain',
        eventDatabaseTransaction.source
      )
      cy.dataCy('project-session-view__active-event--db-execution').should(
        'contain',
        eventDatabaseTransaction.eventable.execution_time_in_milliseconds
      )
      cy.dataCy('project-session-view__active-event--db-sql').should(
        'contain',
        eventDatabaseTransaction.eventable.sql
      )

      const eventClick = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventElementClick'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventClick.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--click-source').should(
        'contain',
        eventClick.source
      )

      cy.dataCy('project-session-view__active-event--click-position-x').should(
        'contain',
        eventClick.eventable.position_x
      )

      cy.dataCy('project-session-view__active-event--click-position-y').should(
        'contain',
        eventClick.eventable.position_y
      )

      if (eventClick.eventable.element_classes) {
        cy.dataCy('project-session-view__active-event--click-classes').should(
          'contain',
          eventClick.eventable.element_classes
        )
      }

      if (eventClick.eventable.inner_text) {
        cy.dataCy('project-session-view__active-event--click-inner-text').should(
          'contain',
          eventClick.eventable.inner_text
        )
      }

      const eventScroll = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventElementScroll'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventScroll.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--scroll-source').should(
        'contain',
        eventScroll.source
      )

      cy.dataCy('project-session-view__active-event--scroll-height').should(
        'contain',
        eventScroll.eventable.scroll_height
      )

      cy.dataCy('project-session-view__active-event--scroll-width').should(
        'contain',
        eventScroll.eventable.scroll_width
      )

      cy.dataCy('project-session-view__active-event--scroll-top').should(
        'contain',
        eventScroll.eventable.scroll_top
      )

      cy.dataCy('project-session-view__active-event--scroll-left').should(
        'contain',
        eventScroll.eventable.scroll_left
      )

      const eventNetworkRequest = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventNetworkRequest'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventNetworkRequest.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--network-source').should(
        'contain',
        eventNetworkRequest.source
      )

      cy.dataCy('project-session-view__active-event--network-url').should(
        'contain',
        eventNetworkRequest.eventable.url
      )

      if (eventNetworkRequest.eventable.request_id) {
        cy.dataCy('project-session-view__active-event--network-request-id').should(
          'contain',
          eventNetworkRequest.eventable.request_id
        )
      } else {
        cy.dataCy('project-session-view__active-event--network-request-id').should(
          'contain',
          'no request id'
        )
      }

      cy.dataCy('project-session-view__active-event--network-method').should(
        'contain',
        eventNetworkRequest.eventable.method
      )

      if (eventNetworkRequest.eventable.response_status) {
        cy.dataCy('project-session-view__active-event--network-response-status').should(
          'contain',
          eventNetworkRequest.eventable.response_status
        )
      } else {
        cy.dataCy('project-session-view__active-event--network-response-status').should(
          'contain',
          'no-response'
        )
      }

      if (eventNetworkRequest.eventable.request_headers) {
        const requestHeaders = JSON.parse(eventNetworkRequest.eventable.request_headers)

        Object.keys(requestHeaders).forEach((key) => {
          cy.dataCy('project-session-view__active-event--network-request-headers').should(
            'contain',
            `${key}: ${requestHeaders[key]}`
          )
        })
      } else {
        cy.dataCy('project-session-view__active-event--network-request-headers').should(
          'contain',
          'No Headers'
        )
      }

      if (eventNetworkRequest.eventable.request_body) {
        cy.dataCy('project-session-view__active-event--network-request-body').should(
          'contain',
          eventNetworkRequest.eventable.request_body
        )
      } else {
        cy.dataCy('project-session-view__active-event--network-request-body').should(
          'contain',
          '<no-body>'
        )
      }

      if (eventNetworkRequest.eventable.response_headers) {
        const requestHeaders = JSON.parse(eventNetworkRequest.eventable.response_headers)

        Object.keys(requestHeaders).forEach((key) => {
          cy.dataCy('project-session-view__active-event--network-response-headers').should(
            'contain',
            `${key}: ${requestHeaders[key]}`
          )
        })
      } else {
        cy.dataCy('project-session-view__active-event--network-response-headers').should(
          'contain',
          'No Headers'
        )
      }

      if (eventNetworkRequest.eventable.response_body) {
        cy.dataCy('project-session-view__active-event--network-response-body').should(
          'contain',
          eventNetworkRequest.eventable.response_body
        )
      } else {
        cy.dataCy('project-session-view__active-event--network-response-body').should(
          'contain',
          '<no-body>'
        )
      }

      const resizeEvent = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventResizeScreen'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${resizeEvent.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--resize-source').should(
        'contain',
        resizeEvent.source
      )

      cy.dataCy('project-session-view__active-event--resize-inner-width').should(
        'contain',
        resizeEvent.eventable.inner_width
      )

      cy.dataCy('project-session-view__active-event--resize-inner-height').should(
        'contain',
        resizeEvent.eventable.inner_height
      )

      const eventUrlChanged = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventUrlChanged'
      )

      cy.get(
        `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventUrlChanged.id}"]`
      ).click()

      cy.dataCy('project-session-view__active-event--url-change-source').should(
        'contain',
        eventUrlChanged.source
      )

      cy.dataCy('project-session-view__active-event--url-change-url').should(
        'contain',
        eventUrlChanged.eventable.to_url
      )
    })
  })

  context('events [bottom tabs]', () => {
    it('should show events for the event type when clicking the tab', () => {
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}`).as('fetchSession')
      cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}/events**`).as(
        'fetchSessionEvents'
      )

      cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

      cy.wait('@fetchSession')

      const dbTransactionsEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventDatabaseTransaction'
      )

      const clickEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventElementClick'
      )

      const scrollEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventElementScroll'
      )

      const logEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventLog'
      )

      const networkRequestEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventNetworkRequest'
      )

      const resizeEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventResizeScreen'
      )

      const changeUrlEvents = sessionWithConvertedVideo.events.filter(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventUrlChanged'
      )

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-db-transaction-events-tab',
        eventType: 'database-transaction',
        events: dbTransactionsEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-click-events-tab',
        eventType: 'element-click',
        events: clickEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-scroll-events-tab',
        eventType: 'scroll',
        events: scrollEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-log-events-tab',
        eventType: 'log',
        events: logEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-network-events-tab',
        eventType: 'network-request',
        events: networkRequestEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-resize-events-tab',
        eventType: 'resize-screen',
        events: resizeEvents
      })

      whenClickingTabEventsShowUp({
        tabIdentifier: 'project-session-view__activate-change-url-events-tab',
        eventType: 'changed-url',
        events: changeUrlEvents
      })
    })
  })

  context('network request', () => {
    it('should open modal displaying resources used when clicking on request id', () => {})
  })
})
