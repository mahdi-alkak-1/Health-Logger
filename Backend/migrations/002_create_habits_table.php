<?php 
require_once('../config/connection.php');

$sql = "CREATE TABLE IF NOT EXISTS habits(
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        name VARCHAR(255) NOT NULL,
        entry_field VARCHAR(255) NOT NULL,
        unit VARCHAR(255) NOT NULL,
        target_value INT(11) NOT NULL,
        is_active BOOLEAN NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id))";

$query = $connection->prepare($sql);
$query->execute();


if(!$query){
    die("Prepare failed: " . $connection->error);
}

if(!$query->execute()){
    die("Execution failed: " . $query->error);
}
echo "habit table created successfully";
?>
