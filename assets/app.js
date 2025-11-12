/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import "./bootstrap.js";
import "./styles/app.css";
import "./styles/app.scss";
import * as bootstrap from "bootstrap"; // Import Bootstrap's JS API
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

// Make Bootstrap available globally for inline scripts
window.bootstrap = bootstrap;

// Enable Bootstrap tooltips
document.addEventListener("DOMContentLoaded", () => {
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    [...tooltipTriggerList].map((el) => new bootstrap.Tooltip(el));
});

// Bootstrap toast example
const toastTrigger = document.getElementById("liveToastBtn");
const toastLiveExample = document.getElementById("liveToast");

if (toastTrigger) {
    const toastBootstrap =
        bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastTrigger.addEventListener("click", () => {
        toastBootstrap.show();
    });
}

// --------------------
// EDITOR.JS SETUP
// --------------------
import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";
import List from "@editorjs/list";
import ImageTool from "@editorjs/image";
import Underline from "@editorjs/underline";
import ImageGallery from "@rodrigoodhin/editorjs-image-gallery";

document.addEventListener("DOMContentLoaded", () => {
    const editorContainer = document.querySelector(".js-editorjs");
    if (!editorContainer) return;

    let initialData = {blocks: []};
    try {
        if (editorContainer.value && editorContainer.value.trim() !== "") {
            initialData = JSON.parse(editorContainer.value);
        }
    } catch (e) {
        console.warn("Invalid JSON in editor content", e);
    }

    const editor = new EditorJS({
        holder: "editorjs-holder",
        tools: {
            header: Header,
            list: List,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: "/editor/upload", // Symfony route
                    },
                },
            },
            imageGallery: {
                class: ImageGallery,
            },
            underline: Underline,
        },
        data: initialData,
        placeholder: "Your Blog content will be here...",
        onChange: async () => {
            const output = await editor.save();
            editorContainer.value = JSON.stringify(output); // Save JSON into hidden textarea
        },
    });

    // ---- Add helper + placeholder to ImageGallery textarea(s) whenever they appear
    (function attachGalleryHelperWatcher() {
        const holder = document.getElementById("editorjs-holder");
        if (!holder) return;

        // Helper that updates a single <textarea>
        const decorateTextarea = (ta) => {
            if (!ta || ta.dataset.galleryDecorated === "1") return;

            // Add placeholder text
            ta.placeholder = `Paste your image URLs here (one per line), example:
https://images.unsplash.com/photo-1501004318641-b39e6451bec6#.jpg
https://wallpapercave.com/wp/wp9100484.jpg`;

            // Add helper note below textarea
            const helper = document.createElement("div");
            helper.className = "gallery-helper";
            helper.textContent = `💡 Tip: Add “#.jpg” at the end of each Unsplash or image URL so it displays properly. Also Press “Shift + Enter” to go to a new line`;
            helper.style.cssText =
                "font-size:0.7rem;color:#666;margin-top:6px;font-style:italic;";

            // Insert right after the textarea
            ta.parentNode && ta.parentNode.insertBefore(helper, ta.nextSibling);

            // Mark as decorated to avoid duplicates
            ta.dataset.galleryDecorated = "1";
        };

        // Decorate any existing textareas (in case gallery already loaded
        holder
            .querySelectorAll(".image-gallery textarea")
            .forEach(decorateTextarea);

        // Watch for future gallery blocks being inserted/edited
        const observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                // Check new nodes
                mutation.addedNodes.forEach((node) => {
                    if (!(node instanceof HTMLElement)) return;

                    //  If new node *is* a textarea
                    if (
                        node.matches &&
                        node.matches(".image-gallery textarea")
                    ) {
                        decorateTextarea(node);
                    }
                    // Also check descendants
                    node.querySelectorAll?.(".image-gallery textarea").forEach(
                        decorateTextarea
                    );
                });
            }
        });

        observer.observe(holder, {childList: true, subtree: true});
    })();
});

// --------------------
// RENDER EDITORJS CONTENT (frontend display)
// --------------------
import edjsHTML from "editorjs-html";

// ✅ Custom parser for ImageGallery
const customParsers = {
    imageGallery: (block) => {
        if (!block.data) return "";

        // handle both possible formats
        const urls = block.data.images || block.data.urls;
        if (!urls || !urls.length) return "";

        // Build responsive gallery grid
        const galleryHTML = urls
            .map((img) => {
                const url = typeof img === "string" ? img : img.url; // support both string and object formats
                return `
                <div class="editorjs-gallery-item">
                    <img src="${url}" alt="gallery image" loading="lazy"/>
                </div>
            `;
            })
            .join("");

        return `
            <div class="editorjs-gallery">
                ${galleryHTML}
            </div>
        `;
    },
};

const edjsParser = edjsHTML(customParsers);

document.addEventListener("DOMContentLoaded", () => {
    const contentElement = document.querySelector(".js-blog-content");
    if (!contentElement) return;

    try {
        const data = JSON.parse(contentElement.dataset.content);
        const html = edjsParser.parse(data);

        // handle both array and string outputs
        if (Array.isArray(html)) {
            contentElement.innerHTML = html.join("");
        } else if (typeof html === "string") {
            contentElement.innerHTML = html;
        } else {
            contentElement.innerHTML = Object.values(html).join("");
        }
    } catch (e) {
        console.error("Error parsing Editor.js content", e);
        contentElement.innerHTML =
            '<p class="text-danger">Failed to render content.</p>';
    }
});

//! FORM previewCoverImage
export function previewCoverImage(event) {
    const file = event.target.files[0];
    const id = event.target.id;
    const previewBox = document.getElementById(`cover-preview-${id}`);

    if (!previewBox) return;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewBox.innerHTML = `<img src="${e.target.result}" alt="Cover preview">`;
        };
        reader.readAsDataURL(file);
    } else {
        previewBox.innerHTML = `<span class="cover-upload-text">Add a cover image</span>`;
    }
}

/**
 * Attach to all cover upload inputs
 */
document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(".cover-upload-input");
    inputs.forEach((input) => {
        input.addEventListener("change", previewCoverImage);
    });
});

//! Sticky categories
window.addEventListener("scroll", () => {
    const header = document.querySelector(".sticky-categories");
    if (window.scrollY > 200) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

//! Multi-select
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select[multiple]').forEach((el) => {
        new TomSelect(el, {
            plugins: ['remove_button'],
            //     create: true,        // 🔒 for now: no “create new” (prevents invalid choice)
            maxItems: 3,
            placeholder: 'Select categories',
        });
    });
});
