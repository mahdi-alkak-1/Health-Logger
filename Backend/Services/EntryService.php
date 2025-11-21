<?php

class EntryService{

    public static function initData(int $userId, array $data, string $rawText){
        $sleep_hours = $data['sleep_hours'] ?? null;
        $steps_count = $data['steps_count'] ?? null;
        $exercise_minutes = $data['exercise_minutes'] ?? null;
        $caffeine_cups = $data['caffeine_cups'] ?? null;
        $water_liters = $data['water_liters'] ?? null;
        $mood_score = $data['mood_score'] ?? null;

        

        
        $entryData = [
            'user_id'          => $userId,
            'raw_text'         => $rawText,
            'sleep_hours'      => $sleep_hours,
            'steps_count'      => $steps_count,
            'exercise_minutes' => $exercise_minutes,
            'caffeine_cups'    => $caffeine_cups,
            'water_liters'     => $water_liters,
            'mood_score'       => $mood_score,
        ];

        return $entryData;

    }

    public static function getEntries(mysqli $connection, int $userId){
        $sql = "SELECT * FROM entries WHERE user_id = ? ORDER BY created_at DESC";
        $input = $connection->prepare($sql);
        $input->bind_param('i', $userId);
        $input->execute();
        $result = $input->get_result();

        $entries = [];
        while ($row = $result->fetch_assoc()) {
            //entering row by row into entries
            $entries[] = $row;
        }

        return $entries;
    }

    public static function updateEntries(mysqli $connection, string $rawText,int $userId, int $entryId, array $data){
        $sleep_hours      = $data['sleep_hours']      ?? null;
        $steps_count      = $data['steps_count']      ?? null;
        $exercise_minutes = $data['exercise_minutes'] ?? null;
        $caffeine_cups    = $data['caffeine_cups']    ?? null;
        $water_liters     = $data['water_liters']     ?? null;
        $mood_score       = $data['mood_score']       ?? null;

        $sql = "UPDATE entries
                SET raw_text = ?, 
                    sleep_hours = ?, 
                    steps_count = ?, 
                    exercise_minutes = ?, 
                    caffeine_cups = ?, 
                    water_liters = ?, 
                    mood_score = ?
                WHERE id = ? AND user_id = ?";

        $input = $connection->prepare($sql);
        $input->bind_param(
            'sdiiiiidi',
            $rawText,
            $sleep_hours,
            $steps_count,
            $exercise_minutes,
            $caffeine_cups,
            $water_liters,
            $mood_score,
            $entryId,
            $userId
        );
               
        if ($input->execute()) {
            return 1;
        }
        
    }

}


?>