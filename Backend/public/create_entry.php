<?php 
require_once('../config/connection.php');
require_once('../services/AuthService.php');
require_once('../services/ResponseService.php');
require_once('../models/Entry.php');


$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
if(!$token){
    echo ResponseService::response(401, "Missing Token");
    exit;
}

$user = AuthService::getUserByToken($connection, $token);
if($user == null){
    echo ResponseService::response(401, "Unauthorized");
    exit;
}

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

$rawText = $data['raw_text'] ?? null;
if ($rawText === null || trim($rawText) === '') {
    echo ResponseService::response(400, "raw_text is required");
    exit;
}
$userId = $user->getId();

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

if ($entryId) {
    echo ResponseService::response(201, "Entry created", ['entry_id' => $entryId]);
    exit;
} else {
    echo ResponseService::response(500, "Failed to create entry");
    exit;
}
?>