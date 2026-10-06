# Practical 11 — Test Cases & CRUD Evaluation Report

**Subject:** ITUE203 Web Development Frameworks  
**Practical:** 11 — Student Management CRUD with Search and Filter  
**Student:** Megh Rana (25CE099)  
**Institution:** CHARUSAT FTE  

---

## 1. Test Execution Matrix

| Test ID | Test Scenario | Action / Input Data | Expected Result | Actual Result | Status |
|:-------:|:--------------|:--------------------|:----------------|:--------------|:------:|
| **TC-CRUD-01** | Display Student Records (Read) | Navigate to `index.php` | Renders responsive table of enrolled students with Roll No, Name, Branch, Year, Status badges, and action buttons. | Table renders properly with 25 seeded records and page navigation. | **PASS** |
| **TC-CRUD-02** | Add New Student (Create) | Roll: `25CE025`<br>Name: `Pooja Joshi`<br>Email: `pooja.j@charusat.edu.in`<br>Mobile: `9825987654`<br>Branch: `btech-cs`<br>Year: `2` | Validated on server. Prepared `INSERT` query executed. Flash message displayed: `"Student 'Pooja Joshi' added successfully!"` | New record created with unique ID; redirects to `index.php`. | **PASS** |
| **TC-CRUD-03** | Duplicate Roll No on Add | Roll: `25CE099` (existing)<br>Other fields: Valid | Validation halts: `"This Roll Number is already registered."` No duplicate inserted. | Rejected; inline error displayed; sticky form preserves input. | **PASS** |
| **TC-CRUD-04** | Edit Student Record (Update) | Modify student #1 mobile to `9876540000` and Year to `3` | Pre-populated form loaded. Prepared `UPDATE` statement executed. Flash banner displayed. | Record updated in MySQL; reflected in table view. | **PASS** |
| **TC-CRUD-05** | Update Duplicate Check | Change student #2 email to student #1's email | Blocked: `"Another student is already using this Email address."` | Rejected; update prevented. | **PASS** |
| **TC-CRUD-06** | Soft Delete Student (Advanced) | Click "Archive" on Active student | Executes `UPDATE students SET status = 'inactive' WHERE id = ?`. Record remains in DB; badge changes to Inactive. | Status updated to Inactive without deleting record or breaking FK relations. | **PASS** |
| **TC-CRUD-07** | Restore Soft-Deleted Student | Click "Activate" on Inactive student | Executes `UPDATE students SET status = 'active' WHERE id = ?`. Badge changes to Active. | Status updated to Active. | **PASS** |
| **TC-CRUD-08** | Permanent Hard Delete | Click "Delete" (trash icon) with confirm dialog | Executes `DELETE FROM students WHERE id = ?`. Record permanently removed. | Record deleted; table refreshed. | **PASS** |
| **TC-CRUD-09** | Search by Full Name | Search query: `"Megh"` | Prepared `LIKE %Megh%` filters table to records containing "Megh". | Returns Megh Rana (25CE099). | **PASS** |
| **TC-CRUD-10** | Search by Roll Number | Search query: `"25CE00"` | Returns all students starting with `25CE001` through `25CE009`. | Exact 9 records returned. | **PASS** |
| **TC-CRUD-11** | Multi-Criteria Filter | Branch: `B.Tech CE`<br>Year: `3`<br>Status: `Active` | Query applies `AND course = 'btech-ce' AND year = 3 AND status = 'active'`. | Returns matching 3rd year CE active students. | **PASS** |
| **TC-CRUD-12** | Pagination Navigation | Navigate from Page 1 to Page 2 with active filter | URL appends `page=2` while retaining all existing filter query parameters. | Page 2 renders remaining records correctly. | **PASS** |
| **TC-CRUD-13** | CSV Dataset Export (Intermediate) | Click "Export CSV" | Streams RFC 4180 formatted CSV with headers and UTF-8 BOM encoding for Excel. | Browser initiates CSV file download matching currently filtered dataset. | **PASS** |
| **TC-CRUD-14** | SQL Injection in Search | Input: `' OR '1'='1' --` | Intercepted safely via prepared statement parameter binding (`$stmt->prepare()` with `?`). | Treated as literal text; returns 0 matches; no SQL syntax error. | **PASS** |

---

## 2. Advanced Extension: Soft Delete vs Hard Delete

1. **Soft Delete (`status = 'inactive'`):**
   - Retains referential integrity with child tables like `registrations` and `users`.
   - Prevents cascading deletion of historic event attendance records.
   - Allows students to be reactivated upon re-admission or semester clearance.
2. **Hard Delete (`DELETE FROM students`):**
   - Removes row permanently.
   - Requires explicit confirmation via modal/alert and verified POST request with valid CSRF token.

---

## 3. Intermediate Extension: CSV Export Implementation

- Implemented in `export_csv.php`.
- Respects active user search keywords and filter dropdowns.
- Includes UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) ensuring correct display of characters in Microsoft Excel.
