<?php

class AuthMiddleware
{
    public static function handle($requiredRole = null)
    {
        session_start();

        // 1. Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header("Location: /login.php");
            exit();
        }

        $user = $_SESSION['user'];

        // 2. If no specific role required → allow any logged-in user
        if ($requiredRole === null) {
            return true;
        }

        // 3. Role-based access control
        if (!isset($user['role'])) {
            http_response_code(403);
            echo "Access denied: Role not defined.";
            exit();
        }

        // 4. Check role match
        if ($user['role'] !== $requiredRole) {
            http_response_code(403);
            echo "Access denied: Unauthorized role.";
            exit();
        }

        return true;
    }
}
