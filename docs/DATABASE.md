# Database Structure

## Tables

### users
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | User ID |
| nama | VARCHAR(255) | Full name |
| email | VARCHAR(255) | Unique email |
| password | VARCHAR(255) | Hashed password |
| role | ENUM('user','admin') | User role |
| created_at | TIMESTAMP | Registration time |

### pengaduan (Complaints)
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | Complaint ID |
| user_id | INT (FK) | Reporter's user ID |
| judul | VARCHAR(255) | Complaint title |
| kategori | ENUM | Category (Fasilitas, Internet, Akademik, Kebersihan, Keamanan, Lainnya) |
| deskripsi | TEXT | Detailed description |
| file_bukti | VARCHAR(255) | Uploaded file name |
| status | ENUM | Status: menunggu, diproses, selesai |
| created_at | TIMESTAMP | Submission time |

## Default Admin
- Email: admin@pnc.ac.id (example)
- Password: hashed with PASSWORD_DEFAULT
