# Practical 12 — Test Cases & File Upload Security Report

**Subject:** ITUE203 Web Development Frameworks  
**Practical:** 12 — Event Management CRUD with Poster Upload and File Validation  
**Student:** Megh Rana (25CE099)  
**Institution:** CHARUSAT FTE  

---

## 1. Test Execution Matrix

| Test ID | Test Scenario | Input Data / Action | Expected Result | Actual Result | Status |
|:-------:|:--------------|:--------------------|:----------------|:--------------|:------:|
| **TC-EVT-01** | Valid Event Creation with Poster | Title: `AI Summit 2026`<br>Category: `Technical`<br>File: `banner.jpg` (650KB) | Validated on server. File randomized and moved to `uploads/`. Event inserted into MySQL. Audit log recorded. | Event created; poster rendered cleanly in console. | **PASS** |
| **TC-EVT-02** | Valid Event Creation without Poster | Title: `Math Club Meet`<br>Category: `Workshop`<br>File: None | Poster is optional. Record inserted with `poster_url = NULL`. Default placeholder shown in UI. | Event created successfully with placeholder banner. | **PASS** |
| **TC-EVT-03** | Oversized Poster Upload (> 2MB) | File: `highres_poster.jpg` (4.8MB) | Validation fails: `"Uploaded image is too large (4.8MB). Maximum allowed size is 2MB."` | Upload aborted; file rejected; form retains text fields. | **PASS** |
| **TC-EVT-04** | Disallowed File Extension | File: `schedule.pdf` or `installer.exe` | Validation fails: `"Invalid file extension. Only JPG, JPEG, PNG, and WEBP formats are accepted."` | File rejected before moving to storage. | **PASS** |
| **TC-EVT-05** | MIME Spoofing / Malicious Shell Upload | File: `payload.php` renamed to `shell.jpg` containing `<?php phpinfo(); ?>` | Intercepted by `finfo_file()` and `getimagesize()`. Detected non-image data. | Rejected: `"Corrupted or non-image file detected."` Malicious upload neutralized. | **PASS** |
| **TC-EVT-06** | Upload Directory Script Execution Blocking | Direct HTTP request: `http://localhost/.../uploads/malicious.php` | Blocked by `uploads/.htaccess` directive (`Deny from all` on script extensions). | Web server issues HTTP 403 Forbidden. Script cannot execute. | **PASS** |
| **TC-EVT-07** | Replace Existing Poster on Edit | Upload replacement `new_poster.png` on event #1 | New poster validated and saved; previous poster file safely deleted from disk via `deletePosterFile()`. | New poster active; no orphaned disk files. | **PASS** |
| **TC-EVT-08** | Remove Poster on Edit | Check `"Remove this poster image"` checkbox | Database `poster_url` updated to `NULL`; existing poster file removed from server disk. | Poster unlinked; placeholder displayed. | **PASS** |
| **TC-EVT-09** | Delete Event (Permanent) | Click Delete with confirmation dialog on event | Event deleted from MySQL; associated poster file unlinked from disk; deletion recorded in `audit_logs`. | Event and poster file purged cleanly. | **PASS** |
| **TC-EVT-10** | Filter by Category & Status | Category: `Technical`<br>Status: `Upcoming` | Query applies `category = 'Technical' AND status = 'upcoming'`. | Only matching upcoming technical events returned. | **PASS** |
| **TC-EVT-11** | Keyword Search | Search: `"Hackathon"` | Prepared `LIKE %Hackathon%` executed. | Returns WebCraft 2026 Hackathon. | **PASS** |

---

## 2. File Upload Security Architecture

### Multi-Tiered Verification Pipeline
1. **Client Constraint:** Form `enctype="multipart/form-data"` with `accept=".jpg,.jpeg,.png,.webp"`.
2. **Server Extension Check:** Whitelist validation (`in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])`).
3. **Real MIME Type Detection:** PHP `finfo_open(FILEINFO_MIME_TYPE)` inspects raw magic byte signatures, ignoring spoofed client headers.
4. **Image Structural Verification:** `getimagesize()` verifies genuine raster dimensions (width & height).
5. **Cryptographic Renaming:** Unique filename generated using `bin2hex(random_bytes(6))` and timestamp, eliminating path traversal (`../`) and file collisions.
6. **Directory Execution Denial:** `uploads/.htaccess` instructs Apache/LiteSpeed to strictly deny execution of `.php`, `.phtml`, `.cgi`, or `.sh` files inside the uploads folder.
