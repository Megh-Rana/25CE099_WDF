# Practical 12 — Event Management CRUD with Poster Upload and File Validation

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO4, CO5  
**Student:** Megh Rana (25CE099)  
**Semester:** 3rd (ODD) 2026-27  

---

## Objective

Develop an administrative Event Management CRUD module for **StudentHub**. Implement event creation, updating, viewing, and deletion backed by MySQL, and incorporate secure multipart file uploads for event posters with strict server-side file type and file size validation, cryptographic file renaming, upload directory execution hardening, and audit logging.

---

## Key Features

- ✅ **Complete Event CRUD Operations:**
  - **Create (`add_event.php`):** Publish new campus events with validated text details and optional poster upload.
  - **Read (`index.php` & `view_event.php`):** Responsive event cards with poster thumbnails, category tags, venue/date badges, and attendee roster.
  - **Update (`edit_event.php`):** Modify event parameters, replace existing posters, or remove posters with automatic disk cleanup.
  - **Delete (`delete_event.php`):** Delete events permanently from MySQL and unlinks the corresponding poster image file from the server disk.
- ✅ **Strict Server-Side File Validation:**
  - **Size Ceiling:** Enforced 2MB maximum file size limit.
  - **Extension Whitelist:** Strictly permits `.jpg`, `.jpeg`, `.png`, and `.webp`.
  - **MIME Inspection:** Verifies raw magic bytes using `finfo_open(FILEINFO_MIME_TYPE)` rather than client-supplied MIME headers.
  - **Image Integrity:** Checks raster dimensions via `getimagesize()` to intercept non-image scripts disguised as images.
- ✅ **Intermediate Extension — Category & Status Filter:**
  - Multi-criteria filtering by Category (Technical, Workshop, Sports, Cultural) and Status (Upcoming, Completed, Cancelled).
- ✅ **Advanced Extension — Upload Security Hardening:**
  - **Cryptographic File Renaming:** Files are renamed to `event_{timestamp}_{random_hash}.ext` to prevent directory traversal attacks and namespace collisions.
  - **Upload Directory Hardening (`uploads/.htaccess`):** Configured Apache rules to strictly deny execution of PHP and executable scripts within the uploads directory.
  - **Audit Logging:** Logs event creation, modifications, and deletions in the MySQL `audit_logs` table.

---

## Key Files

| File | Purpose |
|---|---|
| `index.php` | Main event management console with event cards, search, category/status filters, and action controls |
| `add_event.php` | Event creation form with multipart poster upload and validation |
| `edit_event.php` | Event editor with current poster preview, replacement upload, and removal options |
| `delete_event.php` | Event deletion processor with poster file unlinking and audit logging |
| `view_event.php` | Full-detail event overview with high-resolution poster display and registered attendee table |
| `includes/upload.php` | Centralized secure file upload utility with MIME detection, integrity checks, and disk cleanup |
| `includes/validate_event.php` | Server-side validation rules for event fields |
| `uploads/.htaccess` | Security hardening configuration blocking script execution in uploads folder |
| `events_seed.sql` | Standalone MySQL DDL and sample event records |
| `test_cases.md` | Formal 11-test security matrix verifying file validation, MIME spoofing prevention, and CRUD |

---

## File Upload Security Architecture

```
User Uploads File ($_FILES['poster'])
                  │
                  ▼
         Upload Error Check? (UPLOAD_ERR_OK)
                  │
                  ▼
         File Size <= 2MB?
                  │
                  ▼
         Extension in [jpg, jpeg, png, webp]?
                  │
                  ▼
         finfo_file() MIME is valid image?
                  │
                  ▼
         getimagesize() confirms valid image?
                  │
                  ▼
         Generate Random Name: event_YYYYMMDD_HHMMSS_{random_hex}.jpg
                  │
                  ▼
         move_uploaded_file() into uploads/
                  │
                  ▼
         uploads/.htaccess prevents any script execution!
```

---

## Viva Questions & Answers

1. **Q: Why is relying on `$_FILES['poster']['type']` dangerous for file type validation?**  
   **A:** The `$_FILES['...']['type']` value is sent by the user's browser in the HTTP `Content-Type` header and can be effortlessly forged by attackers (e.g. sending a PHP web shell while claiming `Content-Type: image/jpeg`). The server must independently inspect the actual file bytes using PHP's `finfo_file()` and `getimagesize()`.

2. **Q: Why must uploaded files be renamed rather than keeping original filenames?**  
   **A:** Original filenames can contain path traversal characters (e.g. `../../index.php`), special characters that corrupt filesystems, or malicious extensions (`shell.php.jpg`). Randomizing filenames using timestamps and cryptographic random bytes ensures collision-free storage and completely defeats path traversal.

3. **Q: What role does `.htaccess` play in the uploads folder?**  
   **A:** Even if an attacker somehow bypasses upload filters and saves a script in the uploads folder, `.htaccess` directives (`<FilesMatch "\.(php|phtml|sh)$"> Deny from all </FilesMatch>`) instruct the web server not to execute the file, preventing remote code execution (RCE).
