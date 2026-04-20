<?php

class AuthMiddleware
{
    public static function handle($requiredRole = null)
    {
        if(session_status() === PHP_SESSION_NONE) {
        session_start();
        }
        // 1. Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header("Location: /login.php");
            exit();
        }

        $user = $_SESSION['user'];
        $timeout = 1800; // 1800 seconds = 30 min
         if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
            session_unset();
            session_destroy();
            header("Location: /login.php?message=Session expired");
            exit();
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();

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
         // 8. CSRF protection for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !isset($_SESSION['csrf_token']) ||
                $_POST['csrf_token'] !== $_SESSION['csrf_token']
            ) {
                http_response_code(403);
                echo "Invalid CSRF token.";
                exit();
            }
        }

        return true;
    }
}
