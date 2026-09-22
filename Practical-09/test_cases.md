# Practical 9 — Test Cases & Security Test Report

**Subject:** ITUE203 Web Development Frameworks  
**Practical:** 9 — Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing  
**Student:** Megh Rana (25CE099)  
**Institution:** CHARUSAT FTE  

---

## 1. Test Execution Matrix

| Test ID | Test Scenario | Input Data | Expected Result | Actual Result | Status |
|:-------:|:--------------|:-----------|:----------------|:--------------|:------:|
| **TC-REG-01** | Valid User Registration | Roll: `25CE099`<br>Username: `meghrana`<br>Name: `Megh Rana`<br>Email: `meghrana2007@gmail.com`<br>Mobile: `9876543210`<br>Gender: `male`<br>Course: `btech-ce`<br>Year: `3`<br>Pass: `SecurePass@2026`<br>Confirm: `SecurePass@2026`<br>Terms: `checked` | Registration succeeds. Record inserted in `students` and `users` tables. Password stored as 60-char bcrypt hash. Audit log inserted. | Success alert displayed, database records created, password hashed with bcrypt, audit logged. | **PASS** |
| **TC-REG-02** | Duplicate Email Detection | Email: `meghrana2007@gmail.com` (already in DB)<br>Other fields: Valid | Submission halted with error: `"This Email address is already in use by another student."` | Error returned; zero duplicate rows inserted in `students` or `users`. | **PASS** |
| **TC-REG-03** | Duplicate Username Detection | Username: `meghrana` (already exists)<br>Email: `newemail@charusat.edu.in` | Submission halted with error: `"This username is already taken. Please choose another."` | Error returned; registration rejected. | **PASS** |
| **TC-REG-04** | Duplicate Roll Number Detection | Roll: `25CE099` (already exists)<br>Other fields: Unique | Submission halted with error: `"This Roll Number is already registered."` | Error returned; registration rejected. | **PASS** |
| **TC-REG-05** | Weak Password Complexity | Password: `alllowercasepass` | Validation fails: `"Password must contain uppercase, lowercase, number, and special character (@$!%*?&)."` | Input rejected; not written to DB. | **PASS** |
| **TC-REG-06** | Password Mismatch | Password: `Password@123`<br>Confirm: `Password@999` | Validation fails: `"Passwords do not match."` | Input rejected; sticky form retains fields without passwords. | **PASS** |
| **TC-REG-07** | Invalid Mobile Number | Mobile: `1234567890` (invalid start digit) or `98765` (< 10 digits) | Validation fails: `"Mobile number must be exactly 10 digits starting with 6–9."` | Rejected; clear inline error. | **PASS** |
| **TC-REG-08** | SQL Injection in Fields | Roll: `' OR '1'='1`<br>Username: `admin' --` | Input intercepted by parameterized prepared statements (`$stmt->prepare()` with `?`). | Sanitized literally; fails regex validation; zero SQL syntax errors or injections. | **PASS** |
| **TC-REG-09** | Cross-Site Scripting (XSS) in Name | Name: `<script>alert('XSS')</script>` | Sanitized using `htmlspecialchars()` and regex; rejected due to alphabets-only requirement. | Script rendered inert; safe HTML output. | **PASS** |
| **TC-REG-10** | CSRF Token Tampering | `csrf_token: "invalid_hacked_token"` | Request blocked: `"Security verification failed (invalid or expired CSRF token)."` | Session token comparison failed via `hash_equals()`; operation aborted. | **PASS** |
| **TC-REG-11** | Unchecked Terms & Conditions | `terms: null` | Validation fails: `"You must agree to the Terms & Conditions."` | Form rejected. | **PASS** |

---

## 2. Password Hashing Security Verification

- **Algorithm Used:** `PASSWORD_BCRYPT` (via PHP's native `password_hash()`)
- **Cost Factor:** 10 (standard 2^10 hashing rounds)
- **Salt Generation:** Automatically generates a cryptographically secure pseudo-random salt per hash using CSPRNG.
- **Hash Format Example:**
  ```
  $2y$10$WWLAOuD6jSLhbIV3ch46/u5hU/JQDs/lgMjzagMt23bavVGHbB4J6
  \__/\/ \____________________/\_____________________________/
   |   |           |                           |
   |   |           |                           +-- 31 characters of hash
   |   |           +-- 22 characters of salt
   |   +-- 2-digit cost parameter (10 = 2^10 iterations)
   +-- 2-character bcrypt identifier ($2y$)
  ```
- **Plaintext Passwords Stored:** **0** (no plaintext passwords exist anywhere in the database or logs).

---

## 3. Transaction & Database Integrity

1. **Atomic Dual-Table Insert:**
   - Both student profile (`students`) and authentication credentials (`users`) are saved inside a single `PDO::beginTransaction()` block.
   - If either insert encounters a failure, `PDO::rollBack()` prevents orphan records.
2. **Audit Logging (Advanced Extension):**
   - Every successful registration triggers an insert into `audit_logs` storing user ID, action timestamp, and client IP address.
