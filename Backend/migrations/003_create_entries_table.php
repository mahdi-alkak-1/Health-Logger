<?php 
require_once('../config/connection.php');

$sql = "CREATE TABLE IF NOT EXISTS entries(
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        raw_text TEXT NOT NULL,
        sleep_hours DECIMAL(4,2) DEFAULT NULL,
        steps_count INT(11) DEFAULT NULL ,
        exercise_minutes INT(11) DEFAULT NULL,
        caffeine_cups INT(11) DEFAULT NULL,
        water_liters DECIMAL(4,2) DEFAULT NULL ,
        mood_score INT(11) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id))";

$query = $connection->prepare($sql);
$query->execute();

if(!$query){
    die("Prepare failed: " . $connection->error);
}
if(!$query->execute()){
    die("Execution failed: " . $query->error());
}
echo "entry table created successfully";
?>