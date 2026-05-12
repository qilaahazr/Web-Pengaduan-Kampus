<?php

session_start();
include '../config/koneksi.php';

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    $data = mysqli_fetch_assoc($query);

    if($data){

        if(password_verify($password, $data['password'])){

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
?>

<!DOCTYPE html>
<html>
<head>

    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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