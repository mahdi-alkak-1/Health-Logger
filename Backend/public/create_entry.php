<?php 
require('../config/connection.php');
require('../services/AuthService.php');
require('../services/ResponseService.php');
require('../models/Entry.php');


$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
if(!$token){
    return ResponseService::response(401, "Missing Token");
}

$user = AuthService::getUserByToken($connection, $token);
if($user == null){
    return  ResponseService::response(401, "Unauthorized");
}

$input = file_get_contents('php://input');
$data  = json_decode($input, true);
$rawText = $data['raw_text'] ?? null;

$entryData = [
    'user_id'         => $userId,
    'raw_text'        => $rawText,
    'sleep_hours'     => null,
    'steps_count'     => null,
    'exercise_minutes'=> null,
    'caffeine_cups'   => null,
    'water_liters'    => null,
    'mood_score'      => null,
];


$entryId = Entry::create($connection, $entryData);

if($entryId){
    return  ResponseService::response(201, "Entry created", ['entry_id' => $entryId]);
}
?>