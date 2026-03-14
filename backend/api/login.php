<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        exit();
    }
    
    $query = "SELECT user_id, password, user_type, full_name FROM users WHERE email = ? AND is_active = TRUE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
        exit();
    }
    
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['full_name'] = $user['full_name'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user_id' => $user['user_id'],
        'user_type' => $user['user_type']
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}

$conn->close();
?>