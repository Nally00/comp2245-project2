<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php';

class AuthHandler {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($email, $password) {
        try {
            // Input validation
            if (empty($email) || empty($password)) {
                return ['error' => 'Email and password are required'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'Invalid email format'];
            }

            // Get user from database
            $stmt = $this->pdo->prepare("
                SELECT id, firstname, lastname, email, password 
                FROM Users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Verify credentials
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = ($email === 'admin@project2.com');
                
                return [
                    'success' => true,
                    'user' => [
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'email' => $user['email'],
                        'is_admin' => ($email === 'admin@project2.com')
                    ]
                ];
            }

            return ['error' => 'Invalid credentials'];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['error' => 'Database error occurred'];
        }
    }

    public function logout() {
        // Clear all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        return ['success' => true];
    }

    public function checkAuth() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return ['authenticated' => false];
        }

        // Return user information
        return [
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'firstname' => $_SESSION['firstname'],
                'lastname' => $_SESSION['lastname'],
                'email' => $_SESSION['email'],
                'is_admin' => $_SESSION['is_admin']
            ]
        ];
    }

    private function sanitizeInput($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Create instance of AuthHandler
$auth = new AuthHandler($pdo);

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'login':
                if (isset($data['email']) && isset($data['password'])) {
                    $email = $auth->sanitizeInput($data['email']);
                    $password = $data['password']; // Don't sanitize password before verification
                    echo json_encode($auth->login($email, $password));
                } else {
                    echo json_encode(['error' => 'Email and password are required']);
                }
                break;

            case 'logout':
                echo json_encode($auth->logout());
                break;

            default:
                echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['error' => 'No action specified']);
    }
}
// Handle GET requests for checking authentication status
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($auth->checkAuth());
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>