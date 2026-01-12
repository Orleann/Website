<?php
$host = 'innowacyjne-projekty-inf.mysql.database.azure.com';
$db = 'sigma';
$user = 'azure';
$password = 'PgEEcgO80UC9ix';
$port = '3306';

$conn = new mysqli($host, $user, $password, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

