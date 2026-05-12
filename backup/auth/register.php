<?php

include '../config/koneksi.php';

if(isset($_POST['register'])){

    $nama = $_POST['nama'];
    $email = $_POST['email'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $cek = mysqli_query($conn,
        "SELECT * FROM users
         WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        echo "
        <script>
            alert('Email sudah digunakan');
        </script>
        ";

    } else {

        $query = mysqli_query($conn,
            "INSERT INTO users(
                nama,
                email,
                password
            ) VALUES(
                '$nama',
                '$email',
                '$password'
            )"
        );

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
?>

<!DOCTYPE html>
<html>
<head>

    <title>Register</title>

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
                        Register
                    </h2>

                    <form method="POST">

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
