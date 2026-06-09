<?php


// Laad lokale config indien aanwezig, anders het voorbeeld (standaard XAMPP/MAMP).
$local_config   = __DIR__ . "/db_config.local.php";
$example_config = __DIR__ . "/db_config.example.php";

if (file_exists($local_config)) {
    $db = require $local_config;
} elseif (file_exists($example_config)) {
    $db = require $example_config;
} else {
    $db = ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'bureau_kamer'];
}

$dbhost = $db['host'];
$dbuser = $db['user'];
$dbpass = $db['pass'];
$dbname = $db['name'];

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