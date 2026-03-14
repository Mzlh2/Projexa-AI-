<?php
header('Content-Type: application/json');
require_once '../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $roll_number = isset($_POST['roll_number']) ? trim($_POST['roll_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    if (empty($roll_number) || empty($email) || empty($password) || empty($full_name)) {
        echo json_encode(['success' => false, 'message' => 'All fields required']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email']);
        exit();
    }
    
    $check = "SELECT user_id FROM users WHERE email = ? OR roll_number = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("ss", $email, $roll_number);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email or Roll Number already exists']);
        exit();
    }
    
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    
    $insert = "INSERT INTO users (roll_number, email, password, full_name, phone, user_type) VALUES (?, ?, ?, ?, ?, 'student')";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssss", $roll_number, $email, $hashed, $full_name, $phone);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful', 'user_id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}

$conn->close();
?>