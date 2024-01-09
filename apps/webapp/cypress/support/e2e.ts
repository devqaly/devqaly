// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

declare global {
  namespace Cypress {
    interface Chainable {
      /**
       * Custom command to select DOM element by data-cy attribute.
       * @example
       * cy.dataCy('greeting')
       */
      // @ts-ignore
      dataCy(value: string, attributes?: Record<string, string>): Chainable<JQuery<HTMLElement>>

      /**
       * Checks the value in window's clipboard is the same as the one passed via parameter
       * @example
       * cy.assertValueCopiedToClipboard('value copied to clipboard')
       */
      assertValueCopiedToClipboard(text: string): void
    }
  }
}

Cypress.Commands.add('dataCy', (value, attributes) => {
  let selector = `[data-cy=${value}]`

  if (attributes) {
    selector += Object.keys(attributes).reduce((selectorString, attribute) => {
      return `${selectorString}[${attribute}="${attributes[attribute]}"]`
    }, '')
  }

  return cy.get(selector)
})

Cypress.Commands.add('assertValueCopiedToClipboard', (value) => {
  cy.window().then((win) => {
    win.navigator.clipboard.readText().then((text) => {
      expect(text).to.eq(value)
    })
  })
})

Cypress.on('window:before:load', (win) => {
  let copyText: string

  if (!win.navigator.clipboard) {
    // @ts-ignore
    win.navigator.clipboard = {
      __proto__: {}
    }
  }

  // @ts-ignore
  win.navigator.clipboard.__proto__.writeText = (text) => (copyText = text)
  // @ts-ignore
  win.navigator.clipboard.__proto__.readText = () => new Promise((resolve) => resolve(copyText))
})

// Import commands.js using ES2015 syntax:
import './commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')
