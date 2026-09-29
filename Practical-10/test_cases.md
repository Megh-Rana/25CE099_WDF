# Practical 10 — Test Cases & Authentication Security Report

**Subject:** ITUE203 Web Development Frameworks  
**Practical:** 10 — Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token  
**Student:** Megh Rana (25CE099)  
**Institution:** CHARUSAT FTE  

---

## 1. Test Execution Matrix

| Test ID | Test Scenario | Input / Action | Expected Result | Actual Result | Status |
|:-------:|:--------------|:---------------|:----------------|:--------------|:------:|
| **TC-AUTH-01** | Student Login Authentication | User: `meghrana2007@gmail.com`<br>Pass: `Student@123` | Validated via `password_verify()`. Session initialized. Redirected to `dashboard_student.php`. | Authenticated; redirected to student dashboard. `last_login` updated. | **PASS** |
| **TC-AUTH-02** | Administrator Login Authentication | User: `admin@charusat.edu.in`<br>Pass: `Admin@123` | Validated. Role identified as `'admin'`. Redirected to `dashboard_admin.php`. | Authenticated; redirected to privileged admin dashboard. | **PASS** |
| **TC-AUTH-03** | Incorrect Password | User: `admin@charusat.edu.in`<br>Pass: `WrongPass#999` | Login fails with error: `"Invalid username/email or password."` Failed attempt logged in `audit_logs`. | Form displays error alert; credentials rejected; audit log updated. | **PASS** |
| **TC-AUTH-04** | Non-Existent User | User: `ghost@charusat.edu.in`<br>Pass: `AnyPass@123` | Login fails with generic error. No user enumeration leaked. | Form displays generic error alert. | **PASS** |
| **TC-AUTH-05** | Session Fixation Protection | Log in from anonymous session | Session identifier regenerated via `session_regenerate_id(true)`. Old session destroyed. | New session ID assigned post-authentication. | **PASS** |
| **TC-AUTH-06** | Unauthorized Dashboard Access | Direct GET `dashboard_student.php` without active session | Blocked by `requireRole()`. Redirected to `login.php?msg=unauthorized`. | Header redirect issued; sensitive portal view protected. | **PASS** |
| **TC-AUTH-07** | RBAC Role Violation Attempt | Student user attempts direct GET `dashboard_admin.php` | Blocked by `requireRole(['admin'])`. Redirected to `unauthorized.php` with HTTP 403 Forbidden. | 403 Access Denied page rendered; admin operations protected. | **PASS** |
| **TC-AUTH-08** | Inactivity Session Timeout | Idle user session for > 15 minutes (900s) | Server destroys expired session and redirects to `login.php?msg=timeout`. | Timeout notification banner displayed; re-authentication required. | **PASS** |
| **TC-AUTH-09** | Remember-Me Persistent Login | Check "Remember me" on login | Dual-token (`selector:validator`) generated. SHA-256 hash stored in `remember_tokens`. Cookie stored with 30-day lifetime. | Auto-login succeeds upon new browser session via cookie lookup. | **PASS** |
| **TC-AUTH-10** | Secure User Logout | Click "Sign Out" button | `logoutUser()` deletes DB remember token, unsets cookie, clears `$_SESSION`, destroys session. | Redirected to `login.php?msg=logged_out`. Back button fails to re-enter. | **PASS** |
| **TC-AUTH-11** | CSRF Token Missing in Login | POST to `process_login.php` without token | Request blocked: `"Invalid or expired security token."` | Request rejected before database lookup. | **PASS** |

---

## 2. Authentication Security Architecture

### Session Hijacking & Fixation Defense
- `session_regenerate_id(true)` is executed immediately upon successful password verification, discarding the pre-authentication session ID.
- Cookies configured with `HttpOnly = true` (inaccessible to JavaScript) and `SameSite = 'Strict'` (immune to cross-site request forgery).

### Secure Remember-Me Implementation (Advanced Extension)
- We avoid insecure JWT tokens or storing plaintext user credentials in cookies.
- Two-part random credential architecture:
  1. **Selector (16 bytes random hex):** Public identifier queried via indexed database search.
  2. **Validator (32 bytes random hex):** Only the SHA-256 hash (`hash('sha256', $validator)`) is persisted in `remember_tokens`.
  3. Verification uses constant-time string comparison `hash_equals()`.

### Role-Based Access Control (RBAC) Flow
```
User Requests URL
       │
       ▼
Is User Logged In? ──(No)──► Redirect to login.php?msg=unauthorized
       │ (Yes)
       ▼
Is Session Idle > 15m? ──(Yes)──► Destroy Session & Redirect to login.php?msg=timeout
       │ (No)
       ▼
Does User Role match Allowed Roles?
   ├── YES ──► Render Protected Page
   └── NO  ──► Redirect to unauthorized.php (403 Forbidden)
```
