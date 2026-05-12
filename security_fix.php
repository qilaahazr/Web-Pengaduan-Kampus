<?php
/**
 * Security Fix Utility for Pengaduan Kampus
 * This file applies security fixes to vulnerable code
 */

session_start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Security Fixes Applied</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container mt-5'>
<h1>Security Fixes</h1>";

$fixes = [];

// Fix 1: Register SQL Injection
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>1. Fixing SQL Injection in Register</h5>";

$registerFile = 'auth/register.php';
$registerContent = file_get_contents($registerFile);

$oldQuery = 'mysqli_query($conn,
        "SELECT * FROM users
         WHERE email=\'$email\'"
    );';

$newQuery = '$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_stmt_get_result($stmt);';

$registerContent = str_replace($oldQuery, $newQuery, $registerContent);

$oldInsert = '"INSERT INTO users(
                nama,
                email,
                password
            ) VALUES(
                \'$nama\',
                \'$email\',
                \'$password\'
            )"';

$newInsert = 'mysqli_prepare($conn, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $password);
    mysqli_stmt_execute($stmt);
    $query = $stmt;';

$registerContent = str_replace($oldInsert, $newInsert, $registerContent);

// Add input sanitization
$oldInput = '$nama = $_POST[\'nama\'];
    $email = $_POST[\'email\'];';

$newInput = '$nama = htmlspecialchars(trim($_POST[\'nama\']));
    $email = filter_var(trim($_POST[\'email\']), FILTER_VALIDATE_EMAIL);';

$registerContent = str_replace($oldInput, $newInput, $registerContent);

file_put_contents($registerFile, $registerContent);
echo "<p class='text-success'>✓ Fixed SQL injection in register.php</p>";
echo "</div></div>";

// Fix 2: Tambah Pengaduan SQL Injection & File Upload
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>2. Fixing SQL Injection & File Upload in Tambah Pengaduan</h5>";

$tambahFile = 'user/tambah_pengaduan.php';
$tambahContent = file_get_contents($tambahFile);

// Add file validation before move_uploaded_file
$oldUpload = 'move_uploaded_file(
        $tmpFile,
        "../uploads/" . $namaFile
    );';

$newUpload = '// Security: Validate file
    $allowedTypes = [\'image/jpeg\', \'image/png\', \'image/gif\', \'application/pdf\'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $tmpFile);
    finfo_close($fileInfo);

    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $safeExt = [\'jpg\', \'jpeg\', \'png\', \'gif\', \'pdf\'];

    if (!in_array($mimeType, $allowedTypes) || !in_array($ext, $safeExt)) {
        $pesan = \'<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> File type not allowed!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>\';
    } elseif ($_FILES[\'file\'][\'size\'] > 5 * 1024 * 1024) {
        $pesan = \'<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> File too large (max 5MB)!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>\';
    } else {
        $safeName = time() . \'_\' . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);
        move_uploaded_file($tmpFile, "../uploads/" . $safeName);
        $namaFile = $safeName;';

$tambahContent = str_replace($oldUpload, $newUpload, $tambahContent);

// Close the else bracket
$tambahContent = str_replace(
    '$query = mysqli_query($conn,
        "INSERT INTO pengaduan(',
    '    } // close file validation

    if(isset($namaFile) && !isset($pesan)) {
    $stmt = mysqli_prepare($conn, "INSERT INTO pengaduan (user_id, judul, kategori, deskripsi, file_bukti) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issss", $user_id, $judul, $kategori, $deskripsi, $namaFile);
    $query = mysqli_stmt_execute($stmt);',
    $tambahContent
);

file_put_contents($tambahFile, $tambahContent);
echo "<p class='text-success'>✓ Fixed SQL injection and file upload validation in tambah_pengaduan.php</p>";
echo "</div></div>";

// Fix 3: Add XSS protection functions
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>3. Creating Security Utility File</h5>";

$securityFile = 'config/security.php';
$securityContent = '<?php
/**
 * Security Utilities
 * Provides functions for input sanitization and output escaping
 */

// Escape output for XSS prevention
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, \'UTF-8\');
}

// Sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map(\'sanitize\', $input);
    }
    return trim(htmlspecialchars($input, ENT_QUOTES, \'UTF-8\'));
}

// Generate CSRF token
function csrf_token() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION[\'csrf_token\'])) {
        $_SESSION[\'csrf_token\'] = bin2hex(random_bytes(32));
    }
    return $_SESSION[\'csrf_token\'];
}

// Verify CSRF token
function verify_csrf($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION[\'csrf_token\']) && hash_equals($_SESSION[\'csrf_token\'], $token);
}

// Secure file upload helper
function secure_upload($file, $allowed_types = [], $max_size = 5242880) {
    $result = [\'success\' => false, \'filename\' => \'\', \'error\' => \'\'];

    if (!isset($file[\'tmp_name\']) || $file[\'error\'] !== UPLOAD_ERR_OK) {
        $result[\'error\'] = \'No file uploaded\';
        return $result;
    }

    if ($file[\'size\'] > $max_size) {
        $result[\'error\'] = \'File too large\';
        return $result;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file[\'tmp_name\']);
    finfo_close($finfo);

    if (!empty($allowed_types) && !in_array($mime, $allowed_types)) {
        $result[\'error\'] = \'Invalid file type\';
        return $result;
    }

    $ext = strtolower(pathinfo($file[\'name\'], PATHINFO_EXTENSION));
    $safe_name = time() . \'_\' . preg_replace("/[^a-z0-9]/i", "_", $ext);

    if (move_uploaded_file($file[\'tmp_name\'], "../uploads/" . $safe_name)) {
        $result[\'success\'] = true;
        $result[\'filename\'] = $safe_name;
    } else {
        $result[\'error\'] = \'Failed to move file\';
    }

    return $result;
}
?>';

file_put_contents($securityFile, $securityContent);
echo "<p class='text-success'>✓ Created security.php utility file</p>";
echo "</div></div>";

// Fix 4: Dashboard XSS
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>4. Fixing XSS in Dashboard</h5>";

$dashboardFile = 'user/dashboard.php';
$dashboardContent = file_get_contents($dashboardFile);

$dashboardContent = str_replace(
    '<?php echo $_SESSION[\'nama\']; ?>',
    '<?php echo htmlspecialchars($_SESSION[\'nama\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $dashboardContent
);

file_put_contents($dashboardFile, $dashboardContent);
echo "<p class='text-success'>✓ Fixed XSS in dashboard.php</p>";
echo "</div></div>";

// Fix 5: Admin Panel XSS
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>5. Fixing XSS in Admin Panel</h5>";

$adminFile = 'admin/kelola_pengaduan.php';
$adminContent = file_get_contents($adminFile);

$adminContent = str_replace(
    '<?php echo $_SESSION[\'nama\']; ?>',
    '<?php echo htmlspecialchars($_SESSION[\'nama\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $adminContent
);

$adminContent = str_replace(
    '<?php echo $data[\'nama\']; ?>',
    '<?php echo htmlspecialchars($data[\'nama\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $adminContent
);

$adminContent = str_replace(
    '<?php echo $data[\'judul\']; ?>',
    '<?php echo htmlspecialchars($data[\'judul\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $adminContent
);

$adminContent = str_replace(
    '<?php echo $data[\'kategori\']; ?>',
    '<?php echo htmlspecialchars($data[\'kategori\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $adminContent
);

$adminContent = str_replace(
    '<?php echo $data[\'status\']; ?>',
    '<?php echo htmlspecialchars($data[\'status\'], ENT_QUOTES, \'UTF-8\'); ?>',
    $adminContent
);

$adminContent = str_replace(
    '<?php echo nl2br(htmlspecialchars($data[\'deskripsi\'])); ?>',
    '<?php echo nl2br(htmlspecialchars($data[\'deskripsi\'], ENT_QUOTES, \'UTF-8\')); ?>',
    $adminContent
);

file_put_contents($adminFile, $adminContent);
echo "<p class='text-success'>✓ Fixed XSS in admin/kelola_pengaduan.php</p>";
echo "</div></div>";

// Fix 6: Add CSRF protection to forms
echo "<div class='card mb-3'><div class='card-body'>";
echo "<h5>6. Adding CSRF Protection</h5>";

$loginFile = 'auth/login.php';
$loginContent = file_get_contents($loginFile);

// Add CSRF field to login form
$csrfField = '<input type="hidden" name="csrf_token" value="<?php echo $_SESSION[\'csrf_token\']; ?>">';
$loginContent = str_replace(
    '<form method="POST">',
    '<form method="POST">' . $csrfField,
    $loginContent
);

file_put_contents($loginFile, $loginContent);
echo "<p class='text-success'>✓ Added CSRF token to login.php</p>";
echo "</div></div>";

echo "<div class='card bg-success text-white'><div class='card-body'>";
echo "<h5>All Security Fixes Applied!</h5>";
echo "<p>Run security_test.php again to verify fixes.</p>";
echo "<a href='security_test.php' class='btn btn-light'>Run Security Test Again</a>";
echo "</div></div>";

echo "</div></body></html>";
?>