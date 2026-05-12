# Setup Instructions

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server

## Installation

1. **Database Setup**
   ```sql
   CREATE DATABASE db_pengaduan;
   ```

2. **Import Tables**
   ```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nama VARCHAR(255) NOT NULL,
       email VARCHAR(255) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role ENUM('user','admin') DEFAULT 'user',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE pengaduan (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       judul VARCHAR(255) NOT NULL,
       kategori ENUM('Fasilitas','Internet','Akademik','Kebersihan','Keamanan','Lainnya') NOT NULL,
       deskripsi TEXT NOT NULL,
       file_bukti VARCHAR(255),
       status ENUM('menunggu','diproses','selesai') DEFAULT 'menunggu',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id)
   );
   ```

3. **Environment Configuration**
   Create `.env` file in project root:
   ```
   DB_HOST=localhost
   DB_USER=your_username
   DB_PASS=your_password
   DB_NAME=db_pengaduan
   ```

4. **Create Admin User**
   ```php
   // Or via SQL after creating user table
   INSERT INTO users (nama, email, password, role) 
   VALUES ('Admin', 'admin@example.com', '$2y$10$...', 'admin');
   ```

5. **Configure Web Server**
   - Point document root to project folder
   - Enable mod_rewrite (if using pretty URLs)

## Folder Permissions
```bash
chmod 755 uploads/
```
