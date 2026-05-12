<?php

session_start();

// Generate CSRF token if not exists
if(!isset($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../config/koneksi.php';

if(isset($_POST['login'])){

    // CSRF validation
    $formToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if(empty($formToken) || empty($sessionToken) || !hash_equals($sessionToken, $formToken)){
        echo "<script>alert('CSRF validation failed!');</script>";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if($data){

            if(password_verify($password, $data['password'])){

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                $_SESSION['id'] = $data['id'];
                $_SESSION['nama'] = $data['nama'];
                $_SESSION['role'] = $data['role'];

                if($data['role'] == 'admin'){
                    header("Location: ../admin/kelola_pengaduan.php");
                } else {
                    header("Location: ../user/dashboard.php");
                }
                exit;

            } else {

                echo "
                <script>
                    alert('Password salah');
                </script>
                ";

            }

        } else {

            echo "
            <script>
                alert('Email tidak ditemukan');
            </script>
            ";

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Login</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-md-5">

            <div class="card shadow border-0">

                <div class="card-body p-4">

                    <div class="text-center">

                        <img
                            src="../assets/img/logo.png"
                            class="logo-pnc"
                        >

                    </div>

                    <h2 class="text-center mb-4 mt-3">
                        Login
                    </h2>

                    <form method="POST"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="mb-3">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            name="login"
                            class="btn btn-custom w-100"
                        >
                            Login
                        </button>

                    </form>

                    <div class="text-center mt-3">

                        Belum punya akun?
                        <a href="register.php">
                            Register
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>