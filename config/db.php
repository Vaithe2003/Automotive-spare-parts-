<?php

$conn = mysqli_connect("localhost","root","","spare_parts");

if(!$conn){
    die("Database connection failed: ".mysqli_connect_error());
}

?>