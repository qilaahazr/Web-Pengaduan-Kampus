<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
session_start();
session_destroy();

header("Location: login.php");
exit;
?>
