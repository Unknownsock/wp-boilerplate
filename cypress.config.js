const { defineConfig } = require('cypress');

module.exports = defineConfig({
    e2e: {
        baseUrl: 'http://localhost:8000', // Your WordPress site URL
        setupNodeEvents(on, config) {
        // implement node event listeners here
        },
        specPattern: 'cypress/e2e/**/*.cy.js',
    },
    video: false,
});
