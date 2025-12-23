/// <reference types="cypress" />

describe('Home page', () => {

    beforeEach(() => {
        cy.visit('http://127.0.0.1:8000')
    })


    it('Navbar', () => {
        cy.get("nav").should("exist")
        cy.get("li").eq(0).contains('Home')
    })

    context("Hero Section", () => {
        it('H1 checking', () => {
            cy.getByData("hero").find("h1").contains(/blogs/i)
        })

        it("Categories list Check", () => {
            cy.getByData("hero").find("ul").contains(/fashion/i)
        })
    })

    context("Most read blogs Section", () => {
        it('Blogs list exist', () => {
            cy.getByData("most-read-blogs").getByData("blog-card").should("exist")
        })

    })


})
