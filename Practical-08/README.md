# Practical 8 — MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements

**Subject:** ITUE203 Web Development Frameworks  
**CO Mapping:** CO1, CO5  
**Student:** Megh Rana (25CE099)  
**Semester:** 3rd (ODD) 2026-27  

---

## Objective

Design a normalized relational database for the **StudentHub** portal containing entities for `students`, `events`, `registrations`, `users`, `remember_tokens`, and `audit_logs`. Implement a modular PHP database connection using both PDO (PHP Data Objects) and MySQLi, enforce prepared statements for query parameterization, and provide ER modeling documentation.

---

## Schema & Architecture Overview

The database `studenthub` models campus operations with referential integrity:

- **Entity Normalization:** Evaluated and verified in Third Normal Form (3NF).
- **Primary & Foreign Keys:** Explicit constraints with `ON DELETE CASCADE` on child relations and indexed search fields.
- **Drivers Implemented:**
  - **PDO (`getPDOConnection()`):** Modern object-oriented database abstraction layer with `PDO::ATTR_EMULATE_PREPARES => false` and `ERRMODE_EXCEPTION`.
  - **MySQLi (`getMySQLiConnection()`):** Native MySQL driver with strict error reporting.

```
studenthub
├── students (25 seeded records, roll_no, personal details, course, year, status)
├── events (Campus workshops, hackathons, sports fests)
├── registrations (Associative M:N resolving table linking students and events)
├── users (Student & Admin credentials with bcrypt password hashes)
├── remember_tokens (Hashed persistent login tokens for Practical 10)
└── audit_logs (Security and activity audit trails)
```

---

## Key Files

| File | Purpose |
|---|---|
| `studenthub.sql` | Complete DDL schema, indexes, foreign keys, stored procedures, and 25+ seed records |
| `db.php` | Production-ready database connection module with PDO & MySQLi connection factories |
| `config/db.php` | Modular configuration wrapper |
| `test_connection.php` | Visual and CLI diagnostic tool for connection status, schema stats, and prepared statements |
| `er_diagram.md` | Conceptual & logical ER model diagram (Mermaid), normalization write-up (1NF–3NF), and Data Dictionary |

---

## Intermediate & Advanced Extensions

1. **Intermediate Extension:** Implemented PHP PDO with native prepared statements (`$stmt = $pdo->prepare(...)`), disabling emulated prepares to guard completely against SQL Injection.
2. **Advanced Extension:** Created MySQL Stored Procedures:
   - `sp_register_student_event(p_student_id, p_event_id, OUT p_result)`: Enforces business logic and seat capacity before inserting.
   - `sp_get_student_summary(p_student_id)`: Generates consolidated student metrics.

---

## How to Test & Verify

### 1. Import Database via MySQL CLI or phpMyAdmin
```bash
# Via terminal
mysql -u root -p < studenthub.sql

# Or import studenthub.sql in phpMyAdmin at http://localhost/phpmyadmin
```

### 2. Run Connection Diagnostic
```bash
# Via CLI
/opt/lampp/bin/php test_connection.php

# Or in Browser
http://localhost/25CE099_WDF/Practical-08/test_connection.php
```

---

## Viva Questions & Answers

1. **Q: Why use PDO over legacy `mysql_*` functions?**  
   **A:** PDO supports multiple database engines, provides secure parameterized prepared statements natively, supports exceptions for structured error handling, and `mysql_*` functions were completely removed in PHP 7.0.

2. **Q: What is the difference between emulated prepared statements and native prepared statements?**  
   **A:** Emulated prepared statements (`ATTR_EMULATE_PREPARES => true`) interpolate values inside PHP before sending queries to MySQL. Native prepared statements (`ATTR_EMULATE_PREPARES => false`) transmit query structure and data separately to the database engine, rendering SQL injection impossible.

3. **Q: How does the associative table resolve many-to-many relationships?**  
   **A:** The `registrations` table decomposes the M:N relationship between `students` and `events` into two 1:M relationships using composite or surrogate primary keys and foreign keys referencing each parent table.
