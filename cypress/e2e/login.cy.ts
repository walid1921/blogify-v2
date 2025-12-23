/// <reference types="cypress" />

// This test verifies that a user can successfully navigate to the login page, enter credentials (email and password), submit the form, and be redirected to the dashboard where the dashboard overview heading is displayed. The test confirms that form inputs accept the entered values and that the login flow completes with the expected navigation and page content.


describe("Login Form", () => {

    beforeEach(() => {
        cy.visit('http://127.0.0.1:8000/login')
    })

    it.only("should load the login page and perform login", () => {

        cy.get('form').should('exist');

        // Type into username field
        cy.get('input[name="_username"]').type('admin1@gmail.com').should('have.value', 'admin1@gmail.com');

        // Type into password field
        cy.get('input[name="_password"]').type('AdminPassword123').should('have.value', 'AdminPassword123');

        // Submit the form
        cy.get('button[type="submit"]').click();

        // verifies the button navigates to the correct page
        cy.location("pathname").should("equal", "/dashboard")

        // After login, redirect
        cy.url().should('include', '/dashboard');

        cy.get("h2").should("exist").eq(0).contains(/dashboard overview/i)

    })

    it("doesn't allow a invalid email", () => {
        cy.get('form').should('exist');

        cy.get('input[name="_username"]').type('tjhdgfj1@gmail.com')

        cy.get('input[name="_password"]').type('gfdjgfdj')

        cy.get('button[type="submit"]').click();

        cy.getByData("invalid-message").should('exist');
    })

})
