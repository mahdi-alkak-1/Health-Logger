<?php 

include("Model.php");

class Entry extends Model{

    private int $id;
    private int $user_id;
    private string $raw_text;
    private float $sleep_hours;
    private int $steps_count; 
    private int $exercise_minutes;
    private int  $caffeine_cups;
    private float $water_liters;
    private int $mood_score;
    private string $created_at;
    private string $updated_at;


    protected static string $table = "entries"; 

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->user_id = $data["user_id"];
        $this->raw_text = $data["raw_text"];
        $this->sleep_hours = $data["sleep_hours"];
        $this->steps_count = $data["steps_count"];
        $this->exercise_minutes = $data["exercise_minutes"];
        $this->caffeine_cups = $data["caffeine_cups"];
        $this->water_liters = $data["water_liters"];
        $this->mood_score = $data["mood_score"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
    }

        
    public function toArray(){
        return [
                "id" => $this->id, 
                "user_id" => $this->user_id,
                "raw_text" => $this->raw_text,
                "sleep_hours" => $this->sleep_hours,
                "steps_count" => $this->steps_count,
                "exercise_minutes" => $this->exercise_minutes,
                "caffeine_cups" => $this->caffeine_cups,
                "water_liters" => $this->water_liters,
                "mood_score" => $this->mood_score,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at
            ];
    }
}
?>