<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../models/Habit.php';

class HabitController
{
    // from body: { "name": "Sleep", "entry_field": "sleep_hours", "unit": "hours", "target_value": 8 }
    public function createHabit(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId      = $user->getId();
        $name        = $data['name']        ?? '';
        $entryField  = $data['entry_field'] ?? '';
        $unit        = $data['unit']        ?? '';

        if ($name === '' || $entryField === '' || $unit === '' || !isset($data['target_value'])) {
            return ResponseService::response(400, "name, entry_field, unit and target_value are required");
        }
        $targetValue = (int)$data['target_value'];

        $allowedFields = [
            'sleep_hours',
            'steps_count',
            'exercise_minutes',
            'caffeine_cups',
            'water_liters',
            'mood_score',
        ];

        if (!in_array($entryField, $allowedFields, true)) {
            return ResponseService::response(400, "Invalid entry_field value");
        }

        $habitData = [
            'user_id'      => $userId,
            'name'         => $name,
            'entry_field'  => $entryField,
            'unit'         => $unit,
            'target_value' => $targetValue,
            'is_active'    => 1,
        ];

        $habitId = Habit::create($connection, $habitData);

        if ($habitId) {
            return ResponseService::response(
                201,
                "Habit created",
                ['habit_id' => $habitId]
            );
        }

        return ResponseService::response(500, "Failed to create habit");
    }

    //list the habits
    public function getHabits(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }   

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId = $user->getId();

        $sql = "SELECT * FROM habits WHERE user_id = ? AND is_active = 1 ORDER BY created_at DESC";
        $input = $connection->prepare($sql);
        $input->bind_param('i', $userId);
        $input->execute();
        $result = $input->get_result();

        $habits = [];
        while ($row = $result->fetch_assoc()) {
            $habits[] = $row;
        }

        return ResponseService::response(200, "Habits fetched", $habits);
    }

    
    // Body: { "id": 1, "name": "...", "unit": "...", "target_value": 9, "is_active": 1 }
    public function updateHabit(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId  = $user->getId();
        $habitId = isset($data['id']) ? (int)$data['id'] : 0;

        if ($habitId <= 0) {
            return ResponseService::response(400, "id is required");
        }

        // Make sure habit belongs to this user
        $check = $connection->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
        $check->bind_param('ii', $habitId, $userId);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            return ResponseService::response(404, "Habit not found");
        }

        //fill the new data in:
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = trim($data['name']);
        }
        if (isset($data['unit'])) {
            $updateData['unit'] = trim($data['unit']);
        }
        if (isset($data['target_value'])) {
            $updateData['target_value'] = trim($data['unit']);
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = (int)$data['is_active'];
        }

        if (empty($updateData)) {
            return ResponseService::response(400, "No fields to update");
        }

        // use update of base model
        Habit::update($connection, $habitId, $updateData);

        return ResponseService::response(200, "Habit updated");
    }

    // 
    //delete habit by id
    public function deleteHabit(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId  = $user->getId();
        $habitId = isset($data['id']) ? (int)$data['id'] : 0;

        if ($habitId <= 0) {
            return ResponseService::response(400, "id is required");
        }

        // Only deactivate habit of this user
        $sql = "UPDATE habits SET is_active = 0 WHERE id = ? AND user_id = ?";
        $input = $connection->prepare($sql);
        $input->bind_param('ii', $habitId, $userId);
        $input  ->execute();

        if ($input->affected_rows > 0) {
            return ResponseService::response(200, "Habit deleted");
        }

        return ResponseService::response(404, "Habit not found or already deleted");
    }
}
