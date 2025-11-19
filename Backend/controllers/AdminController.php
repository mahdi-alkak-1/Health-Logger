<?php
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';

class AdminController
{
    private function requireAdmin($connection)
    {
        $token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
        if (!$token) {
            echo ResponseService::response(401, "Missing Token");
            exit;
        }

        $user = AuthService::getUserByToken($connection, $token);
        if ($user === null || $user->getRole() !== 'admin') {
            echo ResponseService::response(403, "Admin only");
            exit;
        }

        return $user;
    }

    public function listEntries()
    {
        global $connection;
        $this->requireAdmin($connection);

        $sql = "
            SELECT 
                e.*,
                u.email
            FROM entries e
            JOIN users u ON e.user_id = u.id
            ORDER BY e.created_at DESC
            LIMIT 200
        ";

        $result = $connection->query($sql);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        echo ResponseService::response(200, "Admin entries", $rows);
        exit;
    }

    public function listHabits()
    {
        global $connection;
        $this->requireAdmin($connection);

        $sql = "
            SELECT 
                h.*,
                u.email
            FROM habits h
            JOIN users u ON h.user_id = u.id
            ORDER BY h.created_at DESC
            LIMIT 200
        ";

        $result = $connection->query($sql);
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        echo ResponseService::response(200, "Admin habits", $rows);
        exit;
    }
}
