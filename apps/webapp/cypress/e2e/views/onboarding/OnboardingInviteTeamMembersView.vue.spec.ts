describe('OnboardingInviteTeamMembersView.vue.spec.ts', () => {
  let companyId: string
  let loggedUser: any
  let project: any

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
      }).then((_project) => {
        project = _project
      })
    })
  })

  it('should have correct link for docs', () => {
    cy.visit(`/onboarding/company/${companyId}/project/${project.id}/inviteTeamMembers`)

    cy.dataCy('onboarding-invite-page__see-docs')
      .should('have.attr', 'href')
      .and('contain', 'https://docs.devqaly.com/getting-started/introduction')

    cy.dataCy('onboarding-invite-page__see-docs').should('have.attr', 'target').and('eq', '_blank')
  })

  context('pre invite members', () => {
    it('should allow to add an email', () => {
      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/inviteTeamMembers`)

      const email1 = `email${Math.random().toString().slice(2, 5)}@email.com`
      const email2 = `email${Math.random().toString().slice(2, 5)}@email.com`

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(email1)

      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__pre-invited-member', {
        'data-email': email1
      }).should('be.visible')

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(email2)

      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__pre-invited-member', {
        'data-email': email2
      }).should('be.visible')

      cy.dataCy('onboarding-invite-page__pre-invited-member').should('have.length', 2)
    })

    it('should allow to remove an email', () => {
      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/inviteTeamMembers`)

      const email1 = `email${Math.random().toString().slice(2, 5)}@email.com`
      const email2 = `email${Math.random().toString().slice(2, 5)}@email.com`

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(email1)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(email2)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__remove-pre-invited-member', {
        'data-email': email1
      }).click()

      cy.dataCy('onboarding-invite-page__pre-invited-member', {
        'data-email': email1
      }).should('not.exist')

      cy.dataCy('onboarding-invite-page__remove-pre-invited-member', {
        'data-email': email2
      }).click()

      cy.dataCy('onboarding-invite-page__pre-invited-member', {
        'data-email': email2
      }).should('not.exist')

      cy.dataCy('onboarding-invite-page__pre-invited-member').should('have.length', 0)
    })

    it('should show warning when no members are invited', () => {
      cy.visit(`/onboarding/company/${companyId}/project/${project.id}/inviteTeamMembers`)

      cy.dataCy('onboarding-invite-page__no-members-invited-helper').should('be.visible')

      cy.dataCy('onboarding-invite-page__add-team-member-email').type('some-email@gaa.com')
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__no-members-invited-helper').should('not.exist')
    })
  })

  context('invite members', () => {
    let inviteMembersProject: any

    beforeEach(() => {
      cy.create({
        model: 'App\\Models\\Project\\Project',
        attributes: { company_id: companyId, created_by_id: loggedUser.id }
      }).then((_project) => {
        inviteMembersProject = _project
      })
    })

    it('should allow to invite users', () => {
      cy.intercept('POST', `**/companies/${companyId}/members`).as('inviteMembersRequest')

      cy.visit(
        `/onboarding/company/${companyId}/project/${inviteMembersProject.id}/inviteTeamMembers`
      )

      const email = `email${Math.random().toString().slice(2, 5)}@email.com`

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(email)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__invite-members-btn').click()

      cy.wait('@inviteMembersRequest').then(({ request, response }) => {
        expect(request.body.emails).to.be.an('array')
        expect(request.body.emails).to.be.length(1)
        expect(request.body.emails[0]).to.be.eq(email)

        expect(response?.statusCode).to.be.eq(204)
      })

      cy.url().should('contain', `projects/${inviteMembersProject.id}/dashboard`)
    })

    it('should allow user to add AND/OR remove emails and invite them', () => {
      cy.intercept('POST', `**/companies/${companyId}/members`).as('inviteMembersRequest')

      cy.visit(
        `/onboarding/company/${companyId}/project/${inviteMembersProject.id}/inviteTeamMembers`
      )

      const invitedEmail = `email${Math.random().toString().slice(2, 5)}@email.com`
      const decoyEmail = `email${Math.random().toString().slice(2, 5)}@email.com`

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(invitedEmail)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(decoyEmail)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__remove-pre-invited-member', {
        'data-email': invitedEmail
      }).click()

      cy.dataCy('onboarding-invite-page__remove-pre-invited-member', {
        'data-email': decoyEmail
      }).click()

      cy.dataCy('onboarding-invite-page__add-team-member-email').type(invitedEmail)
      cy.dataCy('onboarding-invite-page__invite-member').click()

      cy.dataCy('onboarding-invite-page__invite-members-btn').click()

      cy.wait('@inviteMembersRequest').then(({ request, response }) => {
        expect(request.body.emails).to.be.an('array')
        expect(request.body.emails).to.be.length(1)
        expect(request.body.emails[0]).to.be.eq(invitedEmail)

        expect(response?.statusCode).to.be.eq(204)
      })

      cy.url().should('contain', `projects/${inviteMembersProject.id}/dashboard`)
    })
  })
})
