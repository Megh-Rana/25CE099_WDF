# Practical 6 — Rendering External JSON Data using Fetch API, Search, and Filter

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO3, CO4

## Objective

Display events, students, and FAQs from external JSON files using JavaScript Fetch API. Implement dynamic rendering, search, filter, sort, and pagination functionality.

## JSON Data Files

| File | Records | Fields |
|------|---------|--------|
| `data/events.json` | 15 | id, title, date, venue, category, description, status |
| `data/students.json` | 15 | id, name, enrollment, email, course, year, status |
| `data/faqs.json` | 15 | id, question, answer, category |

## Features Implemented

- ✅ Fetch JSON data using `fetch()` API
- ✅ Dynamic HTML rendering with `map()` and `innerHTML`
- ✅ Search by keyword (name, title, question)
- ✅ Filter by category/status dropdown
- ✅ Sort by name/date ascending/descending
- ✅ Client-side pagination (5 items per page)
- ✅ Loading spinner during fetch
- ✅ Error state handling for failed requests
- ✅ Modular JavaScript with separate render functions

## Files

- `data/events.json` — 15 event records
- `data/students.json` — 15 student records
- `data/faqs.json` — 15 FAQ records
- `js/fetch-render.js` — Fetch, search, filter, sort, pagination logic
- `events.html` — Updated with dynamic rendering container
- `admin.html` — Updated with dynamic student list

## Array Methods Used

`fetch()`, `.then()`, `.json()`, `.map()`, `.filter()`, `.sort()`, `.slice()`, `.forEach()`, `.toLowerCase()`, `.includes()`

## Tools Used

JavaScript ES6+, Fetch API, JSON, HTML5, CSS3, Chrome DevTools Network Tab
