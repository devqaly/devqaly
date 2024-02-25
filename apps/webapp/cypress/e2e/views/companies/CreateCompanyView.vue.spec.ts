describe('CreateCompanyView.vue', () => {
  before(() => {
    cy.refreshDatabase()

    cy.login()
  })

  it('should allow user to create company', () => {
    cy.intercept('POST', '**/companies').as('createCompanyRequest')

    cy.visit('/company/create')

    const companyName = 'company name ' + Math.random().toString().slice(2)

    cy.dataCy('create-company-view__company-name').type(companyName)

    cy.dataCy('create-company-view__submit').click()

    cy.wait('@createCompanyRequest').then(({ request, response }) => {
      expect(request.body.name).to.be.eq(companyName)

      expect(response?.statusCode).to.be.eq(201)
      expect(response?.body.data).to.haveOwnProperty('id')
      expect(response?.body.data.name).to.be.eq(companyName)

      cy.url().should('contain', `company/${response?.body.data.id}/projects`)
      cy.url().should('contain', 'showFreeTrialInfo=1')

      cy.dataCy('company-trial-information-dialog').should('be.visible')
    })

    cy.dataCy('navigation-layout__active-company').should('have.text', companyName)
  })
})
