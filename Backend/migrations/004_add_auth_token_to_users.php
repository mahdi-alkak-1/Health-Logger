<?php 

require_once('../config/connection.php');

$sql = "ALTER TABLE users ADD COLUMN auth_token VARCHAR(255) DEFAULT NULL";

$query = $connection->prepare($sql);
$query->execute();

if(!$query->execute()){
    die("Execution failed" . $query->error);
}

echo "auth_token created succ";
    
?>