// Import commands.js using ES2015 syntax
import './commands';

// Prevent uncaught exceptions from failing tests
Cypress.on('uncaught:exception', (err, runnable) => {
    // WordPress often has uncaught exceptions that shouldn't fail tests
    return false;
});

// Log information about the test run
before(() => {
    cy.log('Starting test suite');
});

after(() => {
    cy.log('Test suite completed');
});
