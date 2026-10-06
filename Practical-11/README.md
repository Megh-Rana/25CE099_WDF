# Practical 11 — Student Management CRUD with Search, Filter, and Prepared MySQL Statements

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO4, CO5  
**Student:** Megh Rana (25CE099)  
**Semester:** 3rd (ODD) 2026-27  

---

## Objective

Create a full-featured, secure, and data-driven Student Management CRUD module for **StudentHub**. Implement Create, Read, Update, and Delete operations using parameterized MySQL prepared statements, support multi-criteria searching and filtering, provide paginated table navigation, and incorporate intermediate CSV export and advanced soft-delete functionalities.

---

## Key Features

- ✅ **Complete CRUD Lifecycle:**
  - **Create (`add_student.php`):** Validated student insertion with Roll Number and Email uniqueness verification.
  - **Read (`index.php` & `view_student.php`):** Paginated student directory and full student profile view with enrolled events.
  - **Update (`edit_student.php`):** Pre-populated edit form with exclusion-aware duplicate checks.
  - **Delete (`delete_student.php`):** Permanent deletion protected by CSRF tokens and confirmation dialogs.
- ✅ **Dynamic Search & Filtering:**
  - Real-time keyword search across Roll Number, Full Name, and Email (`LIKE ?`).
  - Multi-select dropdown filters for Branch (CE, CS, IT, EC), Academic Year (1–4), and Status (Active/Inactive).
- ✅ **Pagination:**
  - Configurable page size (8 records per page) with total count indicators and stateful query string preservation.
- ✅ **Intermediate Extension — CSV Export:**
  - `export_csv.php` exports currently filtered student records into an RFC 4180 CSV file with UTF-8 BOM encoding.
- ✅ **Advanced Extension — Soft Delete:**
  - Soft delete toggles student enrollment status (`active` ↔ `inactive`), retaining historical records in foreign-key linked tables (`registrations`).
- ✅ **Security Hardening:**
  - Zero SQL injection vulnerability via PDO native prepared statements (`ATTR_EMULATE_PREPARES => false`).
  - CSRF token protection on all mutation requests.
  - Output sanitization with `htmlspecialchars()` to eliminate XSS.

---

## Key Files

| File | Purpose |
|---|---|
| `index.php` | Main CRUD console with search, filter, pagination, status badges, and action controls |
| `add_student.php` | Student creation form with client/server validation and duplicate checks |
| `edit_student.php` | Student modification form pre-filled with existing data |
| `delete_student.php` | Processor for soft-delete (status toggle) and hard-delete operations |
| `view_student.php` | Comprehensive student profile view including associated event enrollments |
| `export_csv.php` | Streaming RFC 4180 CSV file export of filtered records |
| `students_seed.sql` | Standalone MySQL DDL and 25 realistic seeded student records for CHARUSAT |
| `test_cases.md` | Formal 14-test case matrix covering CRUD operations, filtering, pagination, and security |

---

## CRUD Operations & SQL Query Architecture

### 1. Create (Insert)
```sql
INSERT INTO students (roll_no, fullname, email, mobile, gender, course, year, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?);
```

### 2. Read (Paginated Search & Filter)
```sql
SELECT id, roll_no, fullname, email, mobile, gender, course, year, status 
FROM students 
WHERE (roll_no LIKE ? OR fullname LIKE ? OR email LIKE ?)
  AND course = ? AND year = ? AND status = ?
ORDER BY id ASC 
LIMIT 8 OFFSET ?;
```

### 3. Update
```sql
UPDATE students 
SET roll_no = ?, fullname = ?, email = ?, mobile = ?, gender = ?, course = ?, year = ?, status = ?
WHERE id = ?;
```

### 4. Delete & Soft Delete
```sql
-- Soft Delete (Advanced Extension)
UPDATE students SET status = 'inactive' WHERE id = ?;

-- Hard Delete (Permanent)
DELETE FROM students WHERE id = ?;
```

---

## Viva Questions & Answers

1. **Q: Why is Soft Delete preferred in enterprise relational databases?**  
   **A:** Permanent deletion (`DELETE`) causes cascade deletion or foreign key constraint violations across related tables (e.g., student grades, event attendances, library loans). Soft deletion (`status = 'inactive'`) preserves historical audit trails, maintains data integrity, and permits instant recovery if accidental deletion occurs.

2. **Q: How does pagination prevent database server memory exhaustion?**  
   **A:** Fetching thousands of rows with `SELECT *` consumes substantial server RAM and network bandwidth. Using `LIMIT` and `OFFSET` (or keyset pagination) restricts query execution to only the exact rows needed for the active page view.

3. **Q: Why is exclusion checking necessary when validating uniqueness during an UPDATE?**  
   **A:** When editing student #5, their current roll number or email is already in the database. A naive check (`SELECT id FROM students WHERE roll_no = ?`) would report a duplicate against itself. The query must explicitly exclude the current record (`WHERE roll_no = ? AND id != ?`).
