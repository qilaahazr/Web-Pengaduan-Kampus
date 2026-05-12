# Architecture

## Tech Stack
- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: Bootstrap 5 (local)
- **Session**: PHP native sessions

## Directory Structure
```
pengaduan_kampus/
├── admin/              # Admin controllers
│   ├── kelola_pengaduan.php
│   └── update_status.php
├── auth/               # Authentication
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── session.php
├── user/               # User pages
│   ├── dashboard.php
│   └── tambah_pengaduan.php
├── config/             # Configuration
│   └── koneksi.php
├── assets/             # Static files
│   ├── css/
│   ├── js/
│   └── fonts/
└── uploads/           # File uploads
```

## Request Flow
1. User visits page → session.php checks auth
2. Not logged in → redirect to login.php
3. POST form → server-side validation → process → redirect

## Session Management
- Session starts in auth/session.php
- CSRF token generated if not exists
- Session regenerated on successful login
