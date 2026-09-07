describe('Homepage Tests', () => {
    beforeEach(() => {
        cy.visit('/');
    });

    it('should display the homepage title', () => {
        cy.title().should('include', 'Expected Title');
    });

    it('should have a navigation bar', () => {
        cy.get('nav').should('be.visible');
    });

    it('should display the main content', () => {
        cy.get('main').should('be.visible');
    });

    it('should have a footer', () => {
        cy.get('footer').should('be.visible');
    });
});
