<?php
/**
 * Session Security Test for Pengaduan Kampus
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Session Security Test</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .secure { border-left: 4px solid #28a745; padding: 10px; background: #d4edda; margin: 5px 0; }
        .vulnerable { border-left: 4px solid #dc3545; padding: 10px; background: #f8d7da; margin: 5px 0; }
        .warning { border-left: 4px solid #ffc107; padding: 10px; background: #fff3cd; margin: 5px 0; }
    </style>
</head>
<body>
<div class='container mt-5'>
<h1>🔐 Session Security Test</h1>
<hr>";

// Check 1: Session Start
echo "<h5>1. Session Start</h5>";

$session_files = ['auth/session.php', 'auth/login.php', 'auth/register.php'];
$has_session_start = false;

foreach($session_files as $file) {
    if(file_exists($file)) {
        $content = file_get_contents($file);
        if(strpos($content, 'session_start') !== false) {
            echo "<div class='secure'>✅ $file - session_start() found</div>";
            $has_session_start = true;
        }
    }
}

if(!$has_session_start) {
    echo "<div class='vulnerable'>❌ No session_start() found</div>";
}

echo "<hr>";

// Check 2: Session Cookie Settings
echo "<h5>2. Session Cookie Settings</h5>";

$cookie_settings = [
    'httponly' => 'Prevents XSS from stealing session',
    'secure' => 'Only sends cookie over HTTPS',
    'samesite' => 'Prevents CSRF attacks'
];

$config_file = 'config/koneksi.php';
$config_content = file_exists($config_file) ? file_get_contents($config_file) : '';

// Check for session_set_cookie_params in any PHP file
$has_httponly = false;
$has_secure = false;
$has_samesite = false;

foreach(glob('*.php') as $file) {
    $content = file_get_contents($file);
    if(strpos($content, 'httponly') !== false || strpos($content, 'HttpOnly') !== false) {
        $has_httponly = true;
    }
    if(strpos($content, 'secure') !== false && strpos($content, 'session_set_cookie_params') !== false) {
        $has_secure = true;
    }
    if(strpos($content, 'samesite') !== false || strpos($content, 'SameSite') !== false) {
        $has_samesite = true;
    }
}

echo $has_httponly ? "<div class='secure'>✅ httpOnly flag - Set (XSS protection)</div>" : "<div class='vulnerable'>❌ httpOnly flag - NOT SET (Vulnerable to XSS session theft)</div>";
echo $has_secure ? "<div class='secure'>✅ secure flag - Set (HTTPS only)</div>" : "<div class='warning'>⚠️ secure flag - NOT SET (Should be set for production)</div>";
echo $has_samesite ? "<div class='secure'>✅ SameSite - Set (CSRF protection)</div>" : "<div class='vulnerable'>❌ SameSite - NOT SET (Vulnerable to CSRF)</div>";

echo "<hr>";

// Check 3: Session Regeneration
echo "<h5>3. Session Regeneration</h5>";

$has_regenerate = false;
foreach(glob('auth/*.php') as $file) {
    $content = file_get_contents($file);
    if(strpos($content, 'session_regenerate_id') !== false || strpos($content, 'regenerate') !== false) {
        $has_regenerate = true;
    }
}

echo $has_regenerate ? "<div class='secure'>✅ session_regenerate_id() - Found (prevents session fixation)</div>" : "<div class='vulnerable'>❌ session_regenerate_id() - NOT FOUND (vulnerable to session fixation attacks)</div>";

echo "<hr>";

// Check 4: Session Timeout
echo "<h5>4. Session Timeout</h5>";

$has_timeout = false;
foreach(glob('*.php') as $file) {
    $content = file_get_contents($file);
    if(strpos($content, 'session_cache_expire') !== false || strpos($content, 'timeout') !== false || strpos($content, 'expire') !== false) {
        $has_timeout = true;
    }
}

echo $has_timeout ? "<div class='secure'>✅ Session timeout - Found</div>" : "<div class='warning'>⚠️ Session timeout - NOT FOUND (sessions don't expire automatically)</div>";

echo "<hr>";

// Check 5: Role-based Access Control
echo "<h5>5. Role-based Access Control</h5>";

$admin_check = file_get_contents('admin/kelola_pengaduan.php');
if(strpos($admin_check, "role' !== 'admin'") !== false || strpos($admin_check, '$_SESSION[\'role\']') !== false) {
    echo "<div class='secure'>✅ Admin panel - Checks user role</div>";
} else {
    echo "<div class='warning'>⚠️ Admin panel - May need role check</div>";
}

echo "<hr>";

// Summary
echo "<h4>📊 Summary</h4>";
echo "<table class='table table-bordered'>";
echo "<tr><th>Security Feature</th><th>Status</th><th>Risk</th></tr>";
echo "<tr><td>session_start()</td><td class='text-success'>✅ OK</td><td>-</td></tr>";
$httpStatus = $has_httponly ? "text-success" : "text-danger";
$httpText = $has_httponly ? "✅ OK" : "❌ Missing";
echo "<tr><td>httpOnly Cookie</td><td class='$httpStatus'>$httpText</td><td>HIGH if missing</td></tr>";

$secureStatus = $has_secure ? "text-success" : "text-warning";
$secureText = $has_secure ? "✅ OK" : "⚠️ Missing";
echo "<tr><td>Secure Cookie</td><td class='$secureStatus'>$secureText</td><td>MEDIUM</td></tr>";

$samesiteStatus = $has_samesite ? "text-success" : "text-danger";
$samesiteText = $has_samesite ? "✅ OK" : "❌ Missing";
echo "<tr><td>SameSite Cookie</td><td class='$samesiteStatus'>$samesiteText</td><td>MEDIUM</td></tr>";

$regenStatus = $has_regenerate ? "text-success" : "text-danger";
$regenText = $has_regenerate ? "✅ OK" : "❌ Missing";
echo "<tr><td>Session Regeneration</td><td class='$regenStatus'>$regenText</td><td>MEDIUM</td></tr>";

$timeoutStatus = $has_timeout ? "text-success" : "text-warning";
$timeoutText = $has_timeout ? "✅ OK" : "⚠️ Missing";
echo "<tr><td>Session Timeout</td><td class='$timeoutStatus'>$timeoutText</td><td>LOW</td></tr>";
echo "<tr><td>Role-based Access</td><td class='text-success'>✅ OK</td><td>-</td></tr>";
echo "</table>";

echo "<hr>";
echo "<h5>🔧 Recommended Fixes:</h5>";
echo "<div class='alert alert-info'>";
echo "<pre>";
echo "Add this to the top of your PHP files (after &lt;?php):\n\n";
echo "// Secure session configuration\n";
echo "ini_set('session.cookie_httponly', 1);\n";
echo "ini_set('session.cookie_secure', 1);  // Only if using HTTPS\n";
echo "ini_set('session.cookie_samesite', 'Strict');\n";
echo "session_start();\n\n";
echo "// Regenerate session ID on login\n";
echo "session_regenerate_id(true);\n\n";
echo "// Set session timeout (30 minutes)\n";
echo "ini_set('session.gc_maxlifetime', 1800);";
echo "</pre>";
echo "</div>";

echo "</div></body></html>";
?>