# Security Features

## Implemented Protections

### 1. SQL Injection Prevention
- All database queries use prepared statements
- `mysqli_prepare()` + `mysqli_stmt_bind_param()`
- No string interpolation in SQL queries

### 2. CSRF Protection
- Token generated via `bin2hex(random_bytes(32))`
- Hidden input field in all POST forms
- Validation before form processing
- Uses `hash_equals()` for timing-safe comparison

### 3. XSS Prevention
- `htmlspecialchars()` on all output
- ENT_QUOTES and UTF-8 encoding

### 4. Password Security
- Uses `password_hash()` with PASSWORD_DEFAULT
- `password_verify()` for validation
- Min 8 character requirement

### 5. Session Security
- Session regenerated on login: `session_regenerate_id(true)`
- Session validation on protected pages

### 6. File Upload Security
- MIME type validation via `finfo_file()`
- File size limit: 5MB
- Safe filename generation (timestamp + sanitize)
- Allowed types: image/jpeg, image/png, image/gif, application/pdf

### 7. Access Control
- Role-based access in session
- Admin pages check: `$_SESSION['role'] !== 'admin'`

## Environment Variables
Database credentials stored in `.env`:
- DB_HOST
- DB_USER
- DB_PASS
- DB_NAME

This file is gitignored.
