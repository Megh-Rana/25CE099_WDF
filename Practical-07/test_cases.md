# Practical 7 — Test Cases for Server-Side Form Processing

**Subject:** ITUE203 Web Development Frameworks  
**Practical:** 7 — PHP Form Processing with Server-Side Validation and CSV/JSON File Storage  
**Author:** Megh Rana (25CE099)

---

## 1. Test Cases: Registration Form (`process_registration.php`)

| Test ID | Test Scenario | Input Data | Expected Result | Actual Result | Status |
|:-------:|:--------------|:-----------|:----------------|:--------------|:------:|
| **TC-REG-01** | Valid Registration Submission | Fullname: `Megh Rana`<br>Email: `meghrana2007@gmail.com`<br>Mobile: `9876543210`<br>Gender: `male`<br>Course: `btech-cs`<br>Year: `2`<br>Password: `SecurePass@2026`<br>Confirm: `SecurePass@2026`<br>Terms: `on` | Success page shown. Record appended to `data/registrations.csv` and `data/registrations.json` with hashed password. | Success page rendered, CSV & JSON updated. | **PASS** |
| **TC-REG-02** | Empty Full Name | Fullname: `""`<br>Other fields: Valid | Validation fails with error: `"Full name is required."` | Error returned near field; record not saved. | **PASS** |
| **TC-REG-03** | Full Name with Special Characters / Numbers | Fullname: `Megh123_#` | Validation fails: `"Full name must be 2-50 characters (alphabets and spaces only)."` | Error displayed; rejected. | **PASS** |
| **TC-REG-04** | Invalid Email Format | Email: `megh@invalid` | Validation fails: `"Enter a valid email address."` | Error displayed; rejected. | **PASS** |
| **TC-REG-05** | Mobile Number < 10 Digits | Mobile: `98765` | Validation fails: `"Enter a valid 10-digit mobile number starting with 6-9."` | Error displayed; rejected. | **PASS** |
| **TC-REG-06** | Mobile Number Starting with 1-5 | Mobile: `1234567890` | Validation fails: `"Enter a valid 10-digit mobile number starting with 6-9."` | Error displayed; rejected. | **PASS** |
| **TC-REG-07** | Invalid Course Selection | Course: `mtech-cs` | Validation fails: `"Please select a valid course."` (Whitelist check). | Error displayed; rejected. | **PASS** |
| **TC-REG-08** | Weak Password (< 8 Chars) | Password: `pass` | Validation fails: `"Password must be at least 8 characters."` | Error displayed; rejected. | **PASS** |
| **TC-REG-09** | Password Missing Complexity | Password: `alllowercasepassword` | Validation fails: `"Password must include uppercase, lowercase, digit, and special character."` | Error displayed; rejected. | **PASS** |
| **TC-REG-10** | Password Mismatch | Password: `SecurePass@2026`<br>Confirm: `DifferentPass#2026` | Validation fails: `"Passwords do not match."` | Error displayed; rejected. | **PASS** |
| **TC-REG-11** | Terms & Conditions Unchecked | Terms: `not set` | Validation fails: `"You must agree to the Terms & Conditions."` | Error displayed; rejected. | **PASS** |
| **TC-REG-12** | XSS / HTML Injection in Name | Fullname: `<script>alert('xss')</script>` | Sanitized using `htmlspecialchars()`; rejected due to regex alphabets-only requirement. | Script not executed; sanitized safely. | **PASS** |

---

## 2. Test Cases: Contact Form (`process_contact.php`)

| Test ID | Test Scenario | Input Data | Expected Result | Actual Result | Status |
|:-------:|:--------------|:-----------|:----------------|:--------------|:------:|
| **TC-CON-01** | Valid Contact Form Submission | Name: `Aarav Patel`<br>Email: `aarav.patel@charusat.edu.in`<br>Subject: `Hackathon Query`<br>Message: `Can second year students participate?` | Success page shown. Record appended to `data/contacts.json`. | Success page rendered, JSON updated. | **PASS** |
| **TC-CON-02** | Empty Subject | Subject: `""`<br>Other fields: Valid | Validation fails: `"Subject is required."` | Error displayed; message not sent. | **PASS** |
| **TC-CON-03** | Short Message (< 10 Chars) | Message: `Hi there` | Validation fails: `"Message must be at least 10 characters."` | Error displayed; message not sent. | **PASS** |
| **TC-CON-04** | Invalid Email | Email: `not-an-email` | Validation fails: `"Enter a valid email address."` | Error displayed; message not sent. | **PASS** |
| **TC-CON-05** | Direct GET Request Attempt | Request: `GET /Practical-07/process_contact.php` | Redirected to `contact.html`. | Header redirect issued. | **PASS** |

---

## 3. Storage Verification

1. **CSV Storage (`Practical-07/data/registrations.csv`)**:
   - Headers: `fullname,email,mobile,gender,course,year,registered_at`
   - File opens with `a` flag (append mode) to avoid overwriting existing student records.
   - Values written via `fputcsv()` with RFC 4180 escaping.

2. **JSON Storage (`Practical-07/data/registrations.json` & `contacts.json`)**:
   - Decodes existing JSON array, appends new record, and saves using `json_encode(..., JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)`.
   - Passwords are encrypted with `password_hash($password, PASSWORD_DEFAULT)` and excluded from plaintext JSON.

3. **Viewer Interface (`Practical-07/view_registrations.php`)**:
   - Reads CSV records dynamically and formats as responsive HTML tables.
   - Reads JSON registration records and contact inquiries.
   - Escapes all data using `htmlspecialchars()` against persistent XSS.
