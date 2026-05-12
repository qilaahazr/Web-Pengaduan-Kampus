# Sistem Pengaduan Kampus

## Overview
Web-based complaint management system untuk kampus yang memungkinkan mahasiswa dan staff untuk mengajukan pengaduan terkait fasilitas, internet, akademik, kebersihan, dan keamanan.

## User Roles
1. **User/Mahasiswa** - Can submit complaints, view own complaints
2. **Admin** - View all complaints, update status, manage complaints

## Features
- User registration and login
- Submit complaints with file attachments
- View complaint history and status
- Admin dashboard with complaint table
- Status tracking: Menunggu → Diproses → Selesai

## Pages
- `/auth/login.php` - User login
- `/auth/register.php` - New user registration
- `/user/dashboard.php` - User main page
- `/user/tambah_pengaduan.php` - Submit new complaint
- `/admin/kelola_pengaduan.php` - Admin complaint table
