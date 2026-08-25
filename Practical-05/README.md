# Practical 5 — Registration Form with Frontend Validation and User-Friendly Error Handling

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO3

## Objective

Create a comprehensive student registration form with JavaScript validation using Regular Expressions. Implement real-time validation, password strength meter, and accessible error messages near each field.

## Fields Validated

| Field | Validation Rules | Regex Used |
|-------|-----------------|------------|
| Full Name | 2–50 characters, alphabets and spaces only | `/^[A-Za-z\s]{2,50}$/` |
| Email | Standard email format | `/^[^\s@]+@[^\s@]+\.[^\s@]+$/` |
| Mobile | Exactly 10 digits, starts with 6-9 | `/^[6-9]\d{9}$/` |
| Gender | Must be selected | — |
| Course | Must be selected | — |
| Year | Must be selected | — |
| Password | Min 8 chars, uppercase, lowercase, digit, special char | `/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/` |
| Confirm Password | Must match password | — |
| Terms | Must be checked | — |

## Features

- ✅ Real-time validation on `keyup` and `change` events
- ✅ Password strength meter (Weak / Fair / Strong)
- ✅ Field-level error messages displayed next to each input
- ✅ Form-level validation before submission
- ✅ Success modal on valid submission
- ✅ Accessible `aria-describedby` linking error messages to inputs

## Files

- `js/validate.js` — Registration form validation logic with regex
- `register.html` — Updated with validation-ready structure
- `css/style.css` — Styles for error messages and password strength meter

## Tools Used

HTML5, JavaScript ES6+, Regular Expressions, Chrome DevTools
