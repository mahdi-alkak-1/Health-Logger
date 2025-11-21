<?php

require_once __DIR__ . '/../services/ResponseService.php';

class HabitService{
    public static function IsActive(mysqli $connection, string $name, int $target){

        $input = $connection->prepare("SELECT id FROM habits WHERE name = ?");
        $input->bind_param('s', $name);
        $input->execute();
        $result = $input->get_result();

        if($result){
            $change = $connection->prepare("UPDATE habits SET is_active =1,target_value = ? WHERE name = ?");
            $change->bind_param('is',$target,$name);
            $change->execute();
            return ResponseService::response(200, "Your {$name} habit is active");
        }

    }
    public static function getHabits(mysqli $connection, int $userId){
        $sql = "SELECT * FROM habits WHERE user_id = ? AND is_active = 1 ORDER BY created_at DESC";
        $input = $connection->prepare($sql);
        $input->bind_param('i', $userId);
        $input->execute();
        $result = $input->get_result();

        $habits = [];
        while ($row = $result->fetch_assoc()) {
            $habits[] = $row;
        }

        return $habits;
    }

}


?>