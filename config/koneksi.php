<?php

$conn = mysqli_connect(
    "localhost",
    "aqilah",
    "12345",
    "db_pengaduan"
);

if(!$conn){
    die(mysqli_connect_error());
}

?>
