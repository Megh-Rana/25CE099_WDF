# Practical 7 — PHP Form Processing with Server-Side Validation and CSV/JSON File Storage

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO5

## Objective

Process registration and contact form submissions using PHP. Validate and sanitize inputs on the server side, store records in CSV and JSON file formats, and display success/error messages.

## Features Implemented

- ✅ PHP form processing using `$_POST` superglobal
- ✅ Server-side validation for all fields
- ✅ Input sanitization using `htmlspecialchars()`, `filter_var()`, `trim()`
- ✅ Password hashing using `password_hash()`
- ✅ Storage to CSV file (`data/registrations.csv`)
- ✅ Storage to JSON file (`data/registrations.json`)
- ✅ Contact form storage (`data/contacts.json`)
- ✅ Success/error message display
- ✅ Redirect after POST (PRG pattern)

## PHP Files

| File | Purpose |
|------|---------|
| `process_registration.php` | Handles registration form POST, validates, sanitizes, and stores data |
| `process_contact.php` | Handles contact form POST, validates, and stores to JSON |
| `view_registrations.php` | Displays stored registrations from CSV/JSON |
| `includes/validate.php` | Reusable server-side validation functions |

## Validation Rules (Server-Side)

| Field | Validation |
|-------|-----------|
| Full Name | Required, 2–50 chars, alphabets/spaces only |
| Email | Required, valid email format (filter_var) |
| Mobile | Required, 10 digits, starts with 6–9 |
| Gender | Required, must be male/female/other |
| Course | Required, must be in allowed list |
| Year | Required, must be 1–4 |
| Password | Required, min 8 chars, complexity check |
| Confirm Password | Must match password |
| Terms | Must be accepted |

## File Storage Format

### CSV (`data/registrations.csv`)
```
fullname,email,mobile,gender,course,year,registered_at
Megh Rana,megh@example.com,9876543210,male,btech-cs,2,2026-08-18 10:30:00
```

### JSON (`data/registrations.json`)
```json
[
    {
        "fullname": "Megh Rana",
        "email": "megh@example.com",
        "mobile": "9876543210",
        "gender": "male",
        "course": "btech-cs",
        "year": "2",
        "registered_at": "2026-08-18 10:30:00"
    }
]
```

## Tools Used

PHP 8.x, XAMPP/WAMP, HTML5, Chrome DevTools
