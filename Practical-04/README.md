# Practical 4 — JavaScript DOM Manipulation, Event Handling, and UI Interactivity

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO3, CO4

## Objective

Add dynamic UI components to the StudentHub portal using JavaScript:
- Collapsible FAQ accordion
- Modal popup
- Image/content slider
- Notification banner with dismiss
- Hamburger menu toggle
- Light/dark theme switcher with localStorage persistence

## Dynamic Components Implemented

| Component | Description | Key JS Concepts |
|-----------|-------------|----------------|
| Hamburger Menu | Off-canvas mobile navigation toggle | DOM selection, event listeners, classList |
| FAQ Accordion | Click to expand/collapse answers | Event delegation, toggle |
| Modal Popup | Overlay dialog with close on click/Escape | createElement, keydown listener |
| Content Slider | Auto-rotating announcement slider | setInterval, DOM manipulation |
| Notification Banner | Dismissible alert banners | remove(), transition |
| Theme Switcher | Light/dark mode toggle | classList, localStorage, CSS variables |

## JavaScript Techniques Used

- `document.querySelector()` / `document.querySelectorAll()`
- `addEventListener()` for click, keydown, change, DOMContentLoaded
- `classList.add()`, `classList.remove()`, `classList.toggle()`
- `localStorage.getItem()`, `localStorage.setItem()`
- `setInterval()` for slider auto-rotation
- Event delegation for dynamically rendered elements

## Files

- `js/app.js` — Main JavaScript file with all interactive components
- All HTML pages updated with JS references and theme/dark-mode support

## Tools Used

JavaScript ES6+, HTML5, CSS3, Chrome DevTools Console
