<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($user_type == 'student') {
    $query = "SELECT complaint_id, title, complaint_type, status, created_at 
              FROM complaints WHERE student_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
} else {
    $query = "SELECT complaint_id, student_id, title, complaint_type, status, created_at 
              FROM complaints ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

echo json_encode(['success' => true, 'complaints' => $complaints]);

$conn->close();
?>