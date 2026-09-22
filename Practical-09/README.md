# Practical 9 — Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO3, CO5  
**Student:** Megh Rana (25CE099)  
**Semester:** 3rd (ODD) 2026-27  

---

## Objective

Implement a secure, production-grade student registration module that captures user details, performs strict client-side and server-side validation, verifies uniqueness of Roll Number, Email, and Username using parameterized MySQL queries, hashes passwords using the industry-standard Bcrypt algorithm (`password_hash()`), inserts records transactionally across `students` and `users` tables, and provides clear user feedback.

---

## Security Features Implemented

- ✅ **Bcrypt Password Hashing:** Passwords are never stored in plaintext; hashed via `password_hash($pass, PASSWORD_BCRYPT)` with automatic cryptographically secure per-user salt.
- ✅ **Duplicate Conflict Prevention:** Pre-flight `SELECT` queries check for conflicting Roll Numbers, Emails, and Usernames before executing inserts.
- ✅ **Parameterized Prepared Statements:** All database queries utilize native prepared statements with bound parameters to prevent SQL Injection.
- ✅ **CSRF Protection (Advanced Extension):** Anti-CSRF session token generation and timing-attack-safe validation with `hash_equals()`.
- ✅ **Audit Trail Logging (Advanced Extension):** Successful registrations are logged in `audit_logs` with client IP address and timestamp.
- ✅ **Database Transactions:** Atomic execution across `students` and `users` using `beginTransaction()` and `rollBack()`.
- ✅ **Input Sanitization & Output Escaping:** Strict HTML escaping with `htmlspecialchars()` to eliminate XSS vectors.
- ✅ **Sticky Form Fields:** Retains user input upon validation errors without requiring re-entry.

---

## Key Files

| File | Purpose |
|---|---|
| `register.php` | Responsive registration UI with client validation hints, strength meter, and CSRF field |
| `process_register.php` | Backend request processor, duplicate checks, hashing, transactional insert, audit log |
| `config/db.php` | PDO database connection helper |
| `includes/validate.php` | Server-side validation functions with comprehensive regex rules |
| `includes/csrf.php` | Cryptographically secure CSRF token generation and validation |
| `test_cases.md` | Comprehensive test matrix covering 11 valid, invalid, duplicate, and attack scenarios |

---

## Intermediate & Advanced Extensions

1. **Intermediate Extension:** Implemented Parameterized Prepared Statements across all SELECT and INSERT queries (`$pdo->prepare(...)`).
2. **Advanced Extension:** Integrated Cross-Site Request Forgery (CSRF) protection token and automated registration event insertion into the `audit_logs` table.

---

## Viva Questions & Answers

1. **Q: Why is `md5()` or `sha1()` insecure for password hashing?**  
   **A:** `md5()` and `sha1()` are designed for high-speed file checksums, not password security. Attackers can compute billions of MD5/SHA1 hashes per second using modern GPUs and Rainbow Tables. Bcrypt (`password_hash()`) includes an adjustable work factor (computational cost) and automatic salting, making brute-force attacks infeasible.

2. **Q: How does duplicate email checking prevent race conditions?**  
   **A:** In addition to application-level pre-flight SELECT checks, the MySQL database schema defines a `UNIQUE KEY` constraint on the `email` column. If two requests hit simultaneously, MySQL enforces uniqueness at the storage engine level and rejects duplicate insertion.

3. **Q: Why must transactions be used when inserting into multiple tables?**  
   **A:** If the insert into `students` succeeds but the insert into `users` fails (e.g. server crash or duplicate key), a database without transactions will leave an orphaned student record without login credentials. Using `beginTransaction()` and `rollBack()` ensures atomicity: both succeed or neither does.
