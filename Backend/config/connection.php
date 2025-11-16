<?php 
require_once('../api/headers.php');

$connection = new mysqli ("localhost", "root", "", "health_db");

if($connection->connect_error){
    die("connection error" . $connection->connect_error);
}

?>  