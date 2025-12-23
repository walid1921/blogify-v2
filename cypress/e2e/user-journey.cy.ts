/// <reference types="cypress" />


// This test verifies that a user can navigate to the home page, locate a specific blog post titled "Eiusmod consectetur consectetu" within the most-read blogs section, and click the "Read More" link to access it.

describe("User Journey", () => {
    it("a user can find a blog on the home page and read it", () => {
        cy.visit('http://127.0.0.1:8000')

        cy.getByData("most-read-blogs")
            .find('[data-test="blog-card"]')
            .parent()       // go up to the wrapper
            .filter((index, el) => {
                return el.innerText.match(/Eiusmod consectetur consectetur/i);
            })
            .within(() => {
                cy.contains("a", /read more/i).click();
            });


    })
})
