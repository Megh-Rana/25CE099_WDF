# Practical 10 — Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO5  
**Student:** Megh Rana (25CE099)  
**Semester:** 3rd (ODD) 2026-27  

---

## Objective

Develop enterprise-grade session-based authentication and authorization for the **StudentHub** platform. Validate passwords using `password_verify()`, protect against session fixation via `session_regenerate_id(true)`, enforce Role-Based Access Control (RBAC) separating student and administrator dashboards, implement an automatic 15-minute inactivity session timeout, and provide a persistent, cryptographically secure Remember-Me mechanism using database token hashing.

---

## Key Features

- ✅ **Secure Authentication:** `password_verify()` checks user credentials against bcrypt hashes in MySQL.
- ✅ **Session Fixation Prevention:** Regenerates session ID with `session_regenerate_id(true)` immediately after successful login.
- ✅ **Role-Based Access Control (RBAC):** Distinct dashboards for Student (`dashboard_student.php`) and Administrator (`dashboard_admin.php`). Role enforcement middleware redirects unauthorized access attempts to `unauthorized.php` (403 Forbidden).
- ✅ **Inactivity Session Timeout:** Automatically expires sessions after 15 minutes of inactivity (`SESSION_TIMEOUT_SECONDS = 900`).
- ✅ **Remember-Me Persistent Login (Advanced Extension):** Secure selector/validator token pair stored as a SHA-256 hash in MySQL `remember_tokens` table; validated with `hash_equals()`.
- ✅ **Last-Login Tracking (Intermediate Extension):** Automatically updates `last_login` timestamp in `users` table upon successful login.
- ✅ **Audit Logging:** Logs successful logins, failed attempts, and logouts in the `audit_logs` table.
- ✅ **CSRF Protection:** Anti-CSRF token verification across login submissions.

---

## Key Files

| File | Purpose |
|---|---|
| `login.php` | Login interface with demo credential quick-fillers, remember-me checkbox, and status alerts |
| `process_login.php` | Authentication processor verifying credentials, preventing fixation, and routing by role |
| `logout.php` | Session destruction, cookie invalidation, and DB token cleanup |
| `includes/auth.php` | Authentication middleware: `initSession()`, `loginUser()`, `checkSessionTimeout()`, `requireRole()`, remember-me helpers |
| `dashboard_student.php` | Protected student portal displaying academic profile and registered campus events |
| `dashboard_admin.php` | Protected administrator console displaying platform metrics, audit logs, and navigation shortcuts |
| `unauthorized.php` | 403 Forbidden Access Denied page for role-restricted violation attempts |
| `test_cases.md` | Comprehensive 11-test security matrix verifying authentication, RBAC, timeout, and tokens |

---

## Intermediate & Advanced Extensions

1. **Intermediate Extension:** Implemented `last_login` timestamp tracking in the MySQL `users` table (`UPDATE users SET last_login = NOW() WHERE id = ?`).
2. **Advanced Extension:** Implemented a secure Remember-Me persistent login mechanism using random selector/validator pairs stored as SHA-256 hashes in `remember_tokens`, avoiding insecure custom JWT tokens.

---

## Viva Questions & Answers

1. **Q: Why is `session_regenerate_id(true)` necessary during login?**  
   **A:** Without regenerating the session ID, an attacker who previously set or observed a victim's session cookie (Session Fixation attack) retains access after the victim logs in. `session_regenerate_id(true)` generates a completely new cryptographic session ID and destroys the old unauthenticated session file.

2. **Q: How does the inactivity timeout mechanism work?**  
   **A:** The server records the timestamp of the user's latest interaction in `$_SESSION['last_activity'] = time()`. On each subsequent request, the middleware computes `time() - $_SESSION['last_activity']`. If the difference exceeds the threshold (e.g. 900 seconds), `logoutUser()` is invoked and the user is redirected to the login page with a timeout notice.

3. **Q: Why should Remember-Me tokens be stored as hashes in the database rather than plaintext?**  
   **A:** If an attacker breaches the database, plaintext persistent login tokens would allow them to forge authentication cookies for any user. By storing only the SHA-256 hash in the database, token theft from a DB leak cannot be used directly in browser cookies.
