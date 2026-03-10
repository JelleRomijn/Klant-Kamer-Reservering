<?php


$dbhost = "LOCALHOST";
$dbuser = "root";
$dbpass = "";
$dbname = "bureau_kamer";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);


if ($conn -> connect_errno) {
    echo "Failed to connect to MySQL: " . $conn -> connect_error;
    exit();
}


// define("BASEURL","http://localhost/klant-opdracht-module-4/");



function prettyDump ( $var ) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}