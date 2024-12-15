<?php
session_start();
header('Content-Type: application/json');

// Include database configuration
require_once 'config.php';

// Check if user is logged on and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin@project2.com') {
    
    //if not, stop script
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

//Class to handle user
class UserHandler {
    private $pdo;

    // initialize the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //Get users from database
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("
                SELECT id, firstname, lastname, email, created_at,
                CASE 
                    WHEN created_at > NOW() - INTERVAL 1 DAY THEN 'recent'
                    ELSE 'normal'
                END as status
                FROM Users 
                ORDER BY created_at DESC
            ");
            
            return ['success' => true, 'users' => $stmt->fetchAll()];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['error' => 'Error retrieving users'];
        }
    }

    //Function to add a new user to the database
    public function addUser($userData) {
        try {
            // Validate required fields
            $required = ['firstname', 'lastname', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($userData[$field])) {
                    return ['error' => ucfirst($field) . ' is required'];
                }
            }

            // Validate email format
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                return ['error' => 'Invalid email format'];
            }

            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt->execute([$userData['email']]);
            if ($stmt->rowCount() > 0) {
                return ['error' => 'Email already exists'];
            }

            // Validate password
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $userData['password'])) {
                return ['error' => 'Password must have at least 8 characters, including one number, one letter, and one capital letter'];
            }

            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

            // Insert new user into database
            $stmt = $this->pdo->prepare("
                INSERT INTO Users (firstname, lastname, email, password, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userData['firstname'],
                $userData['lastname'],
                $userData['email'],
                $hashedPassword
            ]);

            return ['success' => true, 'message' => 'User added successfully'];

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['error' => 'Error adding user'];
        }
    }
}

// Handle requests
$handler = new UserHandler($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return list of users
    echo json_encode($handler->getAllUsers());
} 
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add new user
    $postData = $_POST ?: json_decode(file_get_contents('php://input'), true);
    if (!$postData) {

        //if no data found, show an error
        echo json_encode(['error' => 'No data received']);
        exit();
    }
    
    // Sanitize input data
    $userData = array_map(function($item) {
        return htmlspecialchars(trim($item), ENT_QUOTES, 'UTF-8');
    }, $postData);
    
    echo json_encode($handler->addUser($userData));
}
else {
    //if request method isnt GET or POST, show error
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>