<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/EntryService.php';
require_once __DIR__ . '/../services/OpenAIService.php';
require_once __DIR__ . '/../models/Entry.php';

class EntryController
{
     
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

        $userId = $user->getId();
        $rawText = $data['raw_text'] ?? null;
        if ($rawText === null || trim($rawText) === '') {
            return ResponseService::response(400, "raw_text is required");
        }

        $entryData = [];
        $entryData = EntryService::initData($userId, $data, $rawText);

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

        $entries = EntryService::getEntries($connection, $userId);

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
 
        $yes = EntryService::updateEntries($connection, $rawText,$userId, $entryId, $data);
        if($yes){
            return  ResponseService::response(200, "Entry updated");
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
        $input = $connection->prepare($sql);
        $input->bind_param('ii', $entryId, $userId);

        if ($input->execute() && $input->affected_rows > 0) {
            return ResponseService::response(200, "Entry deleted");
        }

        return ResponseService::response(404, "Entry not found or not deleted");
    }

    public function stats(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token) {
            return ResponseService::response(401, "Missing Token");
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            return ResponseService::response(401, "Unauthorized");
        }

        $userId = $user->getId();
        $period = $data['period'] ?? 'week';

        if ($period === 'month') {
            $interval = '30 DAY';
        } else {
            $interval = '8 DAY';
        }

        $sql = "
            SELECT
                DATE(created_at) AS day,
                AVG(sleep_hours)      AS sleep_hours,
                SUM(steps_count)      AS steps_count,
                SUM(exercise_minutes) AS exercise_minutes,
                SUM(caffeine_cups)    AS caffeine_cups,
                AVG(water_liters)     AS water_liters,
                AVG(mood_score)       AS mood_score
            FROM entries
            WHERE user_id = ?
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return ResponseService::response(200, "Stats fetched", $rows);
    }

}
    