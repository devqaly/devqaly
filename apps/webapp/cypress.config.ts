import { defineConfig } from 'cypress'
const fs = require('fs')

export default defineConfig({
  e2e: {
    chromeWebSecurity: false,
    watchForFileChanges: false,
    specPattern: 'cypress/e2e/**/*.{cy,spec}.{js,jsx,ts,tsx}',
    baseUrl: 'http://0.0.0.0:4173',
    setupNodeEvents(on, config) {
      on('task', {
        activateCypressEnvFile() {
          if (fs.existsSync('../api/.env.cypress')) {
            fs.renameSync('../api/.env', '../api/.env.backup')
            fs.renameSync('../api/.env.cypress', '../api/.env')
          }

          return null
        },

        activateLocalEnvFile() {
          if (fs.existsSync('../api/.env.backup')) {
            fs.renameSync('../api/.env', '../api/.env.cypress')
            fs.renameSync('../api/.env.backup', '../api/.env')
          }

          return null
        }
      })
    }
  }
})
