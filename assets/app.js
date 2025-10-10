/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './bootstrap.js';
import './styles/app.css';
import './styles/app.scss';
import * as bootstrap from 'bootstrap';  // Import Bootstrap’s JS API

// Enable Bootstrap tooltips
document.addEventListener("DOMContentLoaded", () => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
});

// Bootstrap toast example
const toastTrigger = document.getElementById('liveToastBtn');
const toastLiveExample = document.getElementById('liveToast');

if (toastTrigger) {
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastTrigger.addEventListener('click', () => {
        toastBootstrap.show();
    });
}

// --------------------
// EDITOR.JS SETUP
// --------------------
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import ImageTool from '@editorjs/image';
import Underline from '@editorjs/underline';
import ImageGallery from '@rodrigoodhin/editorjs-image-gallery';

document.addEventListener('DOMContentLoaded', () => {
    const editorContainer = document.querySelector('.js-editorjs');
    if (!editorContainer) return;

    let initialData = {blocks: []};
    try {
        if (editorContainer.value && editorContainer.value.trim() !== '') {
            initialData = JSON.parse(editorContainer.value);
        }
    } catch (e) {
        console.warn('Invalid JSON in editor content', e);
    }

    const editor = new EditorJS({
        holder: 'editorjs-holder',
        tools: {
            header: Header,
            list: List,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '/editor/upload', // Symfony route
                    },
                },
            },
            imageGallery: {
                class: ImageGallery,
            },
            underline: Underline,
        },
        data: initialData,
        placeholder: 'Your Blog content will be here...',
        onChange: async () => {
            const output = await editor.save();
            editorContainer.value = JSON.stringify(output); // Save JSON into hidden textarea
        },
    });
});

// --------------------
// RENDER EDITORJS CONTENT (frontend display)
// --------------------
import edjsHTML from 'editorjs-html';

// ✅ Custom parser for ImageGallery
const customParsers = {
    imageGallery: (block) => {
        if (!block.data) return '';

        // handle both possible formats
        const urls = block.data.images || block.data.urls;
        if (!urls || !urls.length) return '';

        // Build responsive gallery grid
        const galleryHTML = urls.map(img => {
            const url = typeof img === 'string' ? img : img.url; // support both string and object formats
            return `
                <div class="editorjs-gallery-item">
                    <img src="${url}" alt="gallery image" loading="lazy"/>
                </div>
            `;
        }).join('');

        return `
            <div class="editorjs-gallery">
                ${galleryHTML}
            </div>
        `;
    },
};

const edjsParser = edjsHTML(customParsers);

document.addEventListener('DOMContentLoaded', () => {
    const contentElement = document.querySelector('.js-blog-content');
    if (!contentElement) return;

    try {
        const data = JSON.parse(contentElement.dataset.content);
        const html = edjsParser.parse(data);

        // handle both array and string outputs
        if (Array.isArray(html)) {
            contentElement.innerHTML = html.join('');
        } else if (typeof html === 'string') {
            contentElement.innerHTML = html;
        } else {
            contentElement.innerHTML = Object.values(html).join('');
        }
    } catch (e) {
        console.error('Error parsing Editor.js content', e);
        contentElement.innerHTML = '<p class="text-danger">Failed to render content.</p>';
    }
});
