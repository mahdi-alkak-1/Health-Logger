<?php
// Backend/controllers/EntryController.php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../models/Entry.php';

class EntryController
{
    // CREATE: /entries/create
    public function createEntry(mysqli $connection, ?string $token, array $data): string
    {
        //Check token for authentication purpose
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

       
        $rawText = $data['raw_text'] ?? null;
        if ($rawText === null || trim($rawText) === '') {
            return ResponseService::response(400, "raw_text is required");
        }

        $userId = $user->getId();

        
        $entryData = [
            'user_id'          => $userId,
            'raw_text'         => $rawText,
            'sleep_hours'      => null,
            'steps_count'      => null,
            'exercise_minutes' => null,
            'caffeine_cups'    => null,
            'water_liters'     => null,
            'mood_score'       => null,
        ];

        $entryId = Entry::create($connection, $entryData);

        if ($entryId) {
            return ResponseService::response(
                201,
                "Entry created",
                ['entry_id' => $entryId]
            );
        }

        return ResponseService::response(500, "Failed to create entry");
    }

   
    // Returns all entries for logged-in user
    public function getEntries(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId = $user->getId();

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

        return ResponseService::response(200, "Entries fetched", $entries);
    }


    public function updateEntry(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId  = $user->getId();
        $entryId = isset($data['id']) ? (int)$data['id'] : 0;

        if ($entryId <= 0) {
            return ResponseService::response(400, "id is required");
        }

        // Ensure this entry belongs to the user
        $sql = "SELECT id FROM entries WHERE id = ? AND user_id = ?";
        $check = $connection->prepare($sql);
        $check->bind_param('ii', $entryId, $userId);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            return ResponseService::response(404, "Entry not found");
        }

        //Getting the updated raw text from user
        $rawText = $data['raw_text'] ?? null;
        if ($rawText === null || trim($rawText) === '') {
            return ResponseService::response(400, "raw_text is required");
        }
        //Getting the new data from user
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
            return ResponseService::response(200, "Entry updated");
        }

        return ResponseService::response(500, "Failed to update entry");
    }

    //Delete entry by id
    public function deleteEntry(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId  = $user->getId();
        $entryId = isset($data['id']) ? (int)$data['id'] : 0;

        if ($entryId <= 0) {
            return ResponseService::response(400, "id is required");
        }

        $sql = "DELETE FROM entries WHERE id = ? AND user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ii', $entryId, $userId);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return ResponseService::response(200, "Entry deleted");
        }

        return ResponseService::response(404, "Entry not found or not deleted");
    }
}
    