<?php

include '../config/koneksi.php';

if(isset($_POST['register'])){

    // CSRF validation
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        echo "<script>alert('CSRF validation failed!');</script>";
    } else {
        $nama = htmlspecialchars(trim($_POST['nama']));
        $email_input = trim($_POST['email']);
        $email = filter_var($email_input, FILTER_VALIDATE_EMAIL);

        if($email === false){
            echo "<script>alert('Email tidak valid');</script>";
        } else {

        // Password strength validation
        $password = $_POST['password'];
        if(strlen($password) < 8){
            echo "<script>alert('Password minimal 8 karakter');</script>";
        } else {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $cek = $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($cek) > 0){

            echo "
            <script>
                alert('Email sudah digunakan');
            </script>
            ";

        } else {

            $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $password_hash);
            $query = mysqli_stmt_execute($stmt);

            if($query){

                echo "
                <script>
                    alert('Register berhasil');
                    window.location='login.php';
                </script>
                ";

            } else {

                echo "
                <script>
                    alert('Register gagal');
                </script>
                ";

            }
        }
        }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Register</title>

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
                        Register
                    </h2>

                    <form method="POST">

                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="mb-3">

                            <label>Nama</label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                required
                            >

                        </div>

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
                            name="register"
                            class="btn btn-custom w-100"
                        >
                            Register
                        </button>

                    </form>

                    <div class="text-center mt-3">

                        Sudah punya akun?
                        <a href="login.php">
                            Login
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
