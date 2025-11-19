<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/OpenAIService.php';

class AiController
{
    public function weeklySummary()
    {
        global $connection;

        $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
        if (!$token) {
            echo ResponseService::response(401, "Missing Token");
            exit;
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            echo ResponseService::response(401, "Unauthorized");
            exit;
        }

        $userId = $user->getId();

        $sql = "
            SELECT 
                raw_text,
                sleep_hours,
                steps_count,
                exercise_minutes,
                caffeine_cups,
                water_liters,
                mood_score,
                created_at
            FROM entries
            WHERE user_id = ?
              AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $entries = [];
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }

        if (count($entries) === 0) {
            echo ResponseService::response(200, "No entries this week yet", [
                'summary' => "You don't have any entries for this week yet. Log a few days first!"
            ]);
            exit;
        }

        $summary = OpenAIService::weeklySummary($entries);

        echo ResponseService::response(200, "Weekly summary", [
            'summary' => $summary,
        ]);
        exit;
    }

    public function nutritionCoach()
    {
        global $connection;

        $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
        if (!$token) {
            echo ResponseService::response(401, "Missing Token");
            exit;
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null) {
            echo ResponseService::response(401, "Unauthorized");
            exit;
        }

        $userId = $user->getId();

        $sql = "
            SELECT 
                raw_text,
                sleep_hours,
                steps_count,    
                exercise_minutes,
                caffeine_cups,
                water_liters,
                mood_score,
                created_at
            FROM entries
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $entry = $result->fetch_assoc();

        if (!$entry) {
            echo ResponseService::response(200, "No entries yet", [
                'advice' => "Add at least one entry so I can give you nutrition advice."
            ]);
            exit;
        }

        $advice = OpenAIService::nutritionCoach($entry);

        echo ResponseService::response(200, "Nutrition advice", [
            'advice' => $advice,
        ]);
        exit;
    }
}
