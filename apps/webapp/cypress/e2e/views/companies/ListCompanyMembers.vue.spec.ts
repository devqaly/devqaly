describe('ListCompanyMembers.vue', () => {
  let companyId: string
  let loggedUser: any

  before(() => {
    cy.refreshDatabase()
  })

  beforeEach(() => {
    cy.login({ load: ['companiesMember'] }).then(({ user }) => {
      companyId = user.companies_member[0].company_id
      loggedUser = user
    })
  })

  context('filters', () => {
    it('should fetch data when searching by member name', () => {
      cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')

      const searchByName = loggedUser.first_name

      cy.visit(`company/${companyId}/members`)

      // Wait for the initial page request to fetch members
      cy.wait('@companyMembersRequest')

      cy.dataCy('list-company-members__member-name-filter').type(searchByName)

      cy.wait('@companyMembersRequest').then(({ request, response }) => {
        expect(request.url).to.contain(`memberName=${searchByName}`)

        expect(response?.body.data).to.be.an('array')
        expect(response?.body.data).to.have.length(1)
        expect(response?.body.data[0].member.id).to.be.eq(loggedUser.id)
      })
    })

    it('should fetch data when searching by invited by name', () => {
      cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')

      const searchByName = loggedUser.first_name

      cy.visit(`company/${companyId}/members`)

      // Wait for the initial page request to fetch members
      cy.wait('@companyMembersRequest')

      cy.dataCy('list-company-members__invite-by-name-filter').type(searchByName)

      cy.wait('@companyMembersRequest').then(({ request, response }) => {
        expect(request.url).to.contain(`invitedByName=${searchByName}`)

        expect(response?.body.data).to.be.an('array')
        expect(response?.body.data).to.have.length(1)
        expect(response?.body.data[0].member.id).to.be.eq(loggedUser.id)
      })
    })
  })

  context('invite member', () => {
    it('should close modal when clicking "Cancel"', () => {
      cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')

      cy.visit(`company/${companyId}/members`)

      cy.wait('@companyMembersRequest')

      cy.dataCy('invite-company-member__open-invite-member-dialog').click()

      cy.dataCy('invite-company-member__input-email').should('be.focused')

      cy.dataCy('invite-company-member__close-dialog').click()

      cy.dataCy('invite-company-member__input-email').should('not.exist')
    })

    it('should allow to invite members', () => {
      cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')
      cy.intercept('POST', `**/companies/${companyId}/members`).as('inviteMembersRequest')

      const inviteEmail = `bruno.${Math.random().toString().slice(2, 10)}@devqaly.com`

      cy.visit(`company/${companyId}/members`)

      cy.wait('@companyMembersRequest')

      cy.dataCy('invite-company-member__open-invite-member-dialog').click()

      cy.dataCy('invite-company-member__input-email').should('be.focused')

      cy.dataCy('invite-company-member__invite-button').should('be.disabled')

      cy.dataCy('invite-company-member__input-email').type(inviteEmail)

      cy.dataCy('invite-company-member__add-email-to-invited-emails').click()

      cy.dataCy('invite-company-member__invite-button').click()

      cy.wait('@inviteMembersRequest').then(({ request, response }) => {
        expect(request.body.emails).to.be.an('array')
        expect(request.body.emails).to.be.length(1)
        expect(request.body.emails[0]).to.be.eq(inviteEmail)

        expect(response?.statusCode).to.be.eq(204)
      })

      cy.wait('@companyMembersRequest').then(({ request, response }) => {
        expect(request.url).to.contain('orderByCreatedAt=desc')

        expect(response?.statusCode).to.be.eq(200)
        expect(response?.body.data).to.be.an('array')
        expect(response?.body.data).to.have.length(2)
        expect(response?.body.data[0].registerToken.email).to.be.eq(inviteEmail)
      })

      cy.contains('td', inviteEmail)
    })
  })

  context('pagination', () => {
    it('should display correct values when jumping to next page', () => {
      const totalNumberMembers = 60
      const perPage = 50

      cy.create({
        model: 'App\\Models\\Company\\CompanyMember',
        count: totalNumberMembers,
        attributes: { company_id: companyId, invited_by_id: loggedUser.id }
      }).then(() => {
        cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')

        cy.visit(`company/${companyId}/members`)

        cy.wait('@companyMembersRequest').then(({ request, response }) => {
          expect(request.url).to.contain(`perPage=${perPage}`)

          // We need to account for the owner of the company
          expect(response?.body.meta.total).to.be.eq(totalNumberMembers + 1)
        })

        cy.get('.p-paginator-page[aria-label="2"]').click()

        cy.wait('@companyMembersRequest').then(({ request, response }) => {
          expect(request.url).to.contain('page=2')
          expect(request.url).to.contain('perPage=50')

          expect(response?.statusCode).to.be.eq(200)
          // We need to account for the owner of the company
          expect(response?.body.meta.total).to.be.eq(totalNumberMembers + 1)
          expect(response?.body.meta.current_page).to.be.eq(2)
          expect(response?.body.data).to.be.an('array')
          // We need to account for the owner of the company
          expect(response?.body.data).to.have.length(totalNumberMembers - perPage + 1)
        })
      })
    })
  })

  it('should display the members of a company', () => {
    cy.intercept('GET', `**/companies/${companyId}/members**`).as('companyMembersRequest')

    cy.visit(`company/${companyId}/members`)

    cy.wait('@companyMembersRequest').then(({ request, response }) => {
      expect(request.url).to.contain('orderByCreatedAt=desc')
      expect(request.url).to.contain('perPage=50')

      expect(response?.statusCode).to.be.eq(200)
      expect(response?.body.meta.total).to.be.eq(1)
      expect(response?.body.data).to.be.an('array')
      expect(response?.body.data).to.have.length(1)
      // Users that have created the company are "invited" by themselves
      expect(response?.body.data[0].invitedBy.id).to.be.eq(loggedUser.id)
      expect(response?.body.data[0].member.id).to.be.eq(loggedUser.id)
    })

    cy.contains('td', `${loggedUser.first_name} ${loggedUser.last_name}`)
  })
})
