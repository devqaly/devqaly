import differenceInSeconds from 'date-fns/differenceInSeconds'
import isSameSecond from 'date-fns/isSameSecond'
import isSameMinute from 'date-fns/isSameMinute'
import isSameHour from 'date-fns/isSameHour'
import isSameDay from 'date-fns/isSameDay'
import isSameMonth from 'date-fns/isSameMonth'
import isSameYear from 'date-fns/isSameYear'

function selectTab(tabIdentifier: string) {
  cy.dataCy(tabIdentifier).click()
}

function whenClickingTabEventsShowUp({
  tabIdentifier,
  eventType,
  events
}: {
  tabIdentifier: string
  eventType: string
  events: any[]
}) {
  selectTab(tabIdentifier)

  cy.get(
    `[data-cy="project-session-view__bottom-events-section"] [data-cy="list-event__event"][data-event-type="${eventType}"]`
  )
    .should('have.length', events.length)
    .and('be.visible')

  events.forEach((event) => {
    cy.get(
      `[data-cy="project-session-view__bottom-events-section"] [data-event-id="${event.id}"]`
    ).should('be.visible')
  })
}

function openEventDetailsFromLivePreview(eventId: string) {
  cy.get(
    `[data-cy="project-session-view__live-preview-section"] [data-cy="list-event__open-details"][data-event-id="${eventId}"]`
  ).click()
}

function shouldContainSourceInActiveEvent({
  source,
  sourceId
}: {
  source: string
  sourceId: string
}) {
  cy.dataCy(sourceId).should('contain', source)
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
      // If the filesystem is using `S3` adapter, the `src` attribute will have a
      // link to amazon S3 bucket and, it will be something like "{sessionId}.webm".
      // If the filesystem is using the `local` adapter, the `src` attribute will have a
      // link to route that will be handling the streaming of the video.
      // For both reasons above, we will simply check
      // if the `src` contains the session id
      .and('contain', `${sessionWithConvertedVideo.id}`)

    cy.dataCy('project-session-view__live-preview-section').should('be.visible')

    // In the preview mode we should be able to see NUMBER_EVENT_TYPES events
    // Since we are creating one event of each type of event
    cy.dataCy('project-session-view__live-preview-section').within(() => {
      cy.dataCy('list-event__event').should('have.length', NUMBER_EVENT_TYPES)
    })

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

      const VIDEO_PARTITION_SIZES = 10

      cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

      cy.wait('@fetchSession')

      // E.g.: The video starts at 10:00:00, the first partition
      // is trying to get events created at 09:59:50
      cy.wait('@fetchSessionEvents').then(({ request, response }) => {
        expect(request.url).to.include('endCreatedAt=')
        expect(request.url).to.include('startCreatedAt=')

        const url = new URL(request.url)
        const videoStart = new Date(sessionWithConvertedVideo.created_at)
        const deltaStart = new Date(url.searchParams.get('startCreatedAt') as string)
        const deltaEnd = new Date(url.searchParams.get('endCreatedAt') as string)

        expect(differenceInSeconds(deltaEnd, videoStart)).to.be.eq(VIDEO_PARTITION_SIZES)
        expect(isSameSecond(deltaStart, videoStart)).to.be.true
        expect(isSameMinute(deltaStart, videoStart)).to.be.true
        expect(isSameHour(deltaStart, videoStart)).to.be.true
        expect(isSameDay(deltaStart, videoStart)).to.be.true
        expect(isSameMonth(deltaStart, videoStart)).to.be.true
        expect(isSameYear(deltaStart, videoStart)).to.be.true

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
      cy.intercept('GET', `**/sessions/${sessionWithConvertedVideo.id}/video`, {
        fixture: 'test-video.webm,null'
      }).as('videoRequest')

      cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

      cy.dataCy('project-session-view__video')
        .should('have.prop', 'paused', true)
        .and('have.prop', 'ended', false)
        .then(($video) => {
          // @ts-ignore
          $video[0].play()
        })

      cy.dataCy('project-session-view__video')
        .should('have.prop', 'paused', false)
        .and('have.prop', 'ended', false)

      cy.get('video').should('have.prop', 'duration', 32.48)

      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(2000)

      cy.dataCy('project-session-view__video').then(($video) => {
        // @ts-ignore
        $video[0].currentTime = 20000
      })
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

      openEventDetailsFromLivePreview(eventLog.id)
      shouldContainSourceInActiveEvent({
        source: eventLog.source,
        sourceId: 'project-session-view__active-event--log-source'
      })

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

      openEventDetailsFromLivePreview(eventDatabaseTransaction.id)
      shouldContainSourceInActiveEvent({
        source: eventDatabaseTransaction.source,
        sourceId: 'project-session-view__active-event--db-source'
      })

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

      openEventDetailsFromLivePreview(eventClick.id)
      shouldContainSourceInActiveEvent({
        source: eventClick.source,
        sourceId: 'project-session-view__active-event--click-source'
      })

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

      openEventDetailsFromLivePreview(eventScroll.id)
      shouldContainSourceInActiveEvent({
        source: eventScroll.source,
        sourceId: 'project-session-view__active-event--scroll-source'
      })

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

      openEventDetailsFromLivePreview(eventNetworkRequest.id)
      shouldContainSourceInActiveEvent({
        source: eventNetworkRequest.source,
        sourceId: 'project-session-view__active-event--network-source'
      })

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

      openEventDetailsFromLivePreview(resizeEvent.id)
      shouldContainSourceInActiveEvent({
        source: resizeEvent.source,
        sourceId: 'project-session-view__active-event--resize-source'
      })

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

      openEventDetailsFromLivePreview(eventUrlChanged.id)
      shouldContainSourceInActiveEvent({
        source: eventUrlChanged.source,
        sourceId: 'project-session-view__active-event--url-change-source'
      })

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
    it('should open modal displaying resources used when clicking on request id', () => {
      const requestEvent = sessionWithConvertedVideo.events.find(
        (e: any) => e.event_type === 'App\\Models\\Session\\Event\\EventNetworkRequest'
      )

      let databaseTransactionEvent: any

      cy.create({
        model: 'App\\Models\\Session\\Event\\EventDatabaseTransaction',
        attributes: { request_id: requestEvent.eventable.request_id }
      })
        .then((_databaseTransaction) => (databaseTransactionEvent = _databaseTransaction))
        .then(() =>
          cy.create({
            model: 'App\\Models\\Session\\Event',
            attributes: {
              session_id: sessionWithConvertedVideo.id,
              event_type: 'App\\Models\\Session\\Event\\EventDatabaseTransaction',
              event_id: databaseTransactionEvent.id
            }
          })
        )
        .then(() => {
          cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}`).as('fetchSession')
          cy.intercept('GET', `**/api/sessions/${sessionWithConvertedVideo.id}/events**`).as(
            'fetchSessionEvents'
          )

          cy.intercept(
            'GET',
            `api/events/networkRequest/byRequestId/${requestEvent.eventable.request_id}/databaseTransactions**`
          ).as('fetchDatabaseTransactions')
          cy.intercept(
            'GET',
            `api/events/networkRequest/byRequestId/${requestEvent.eventable.request_id}/logs**`
          ).as('fetchLogs')

          cy.visit(`projects/${project.id}/sessions/${sessionWithConvertedVideo.id}`)

          cy.wait(['@fetchSession', '@fetchSessionEvents'])

          selectTab('project-session-view__activate-network-events-tab')

          cy.get(
            `[data-cy="project-session-view__bottom-events-section"] [data-cy="list-event__open-details"][data-event-id=${requestEvent.id}]`
          ).click()

          cy.contains(
            '[data-cy="project-session-view__active-event--network-request-id"]',
            requestEvent.eventable.request_id
          ).click()

          cy.wait(['@fetchDatabaseTransactions', '@fetchLogs'])

          cy.dataCy('project-session-view__active-network-request-sidebar').should('be.visible')

          cy.dataCy('project-session-view__active-network-request-sidebar--db-transaction-title')
            .should('have.attr', 'data-total-number-database-transactions')
            .and('contain', 1)

          cy.dataCy('project-session-view__active-network-request-sidebar--logs-title')
            .should('have.attr', 'data-total-number-logs')
            .and('contain', 0)

          cy.dataCy('project-session-view__active-network-request-sidebar--db-transaction').should(
            'contain',
            databaseTransactionEvent.sql
          )

          cy.dataCy('project-session-view__active-network-request-sidebar--log').should(
            'have.length',
            0
          )
        })
    })
  })
})
