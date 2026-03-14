<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT notification_id, title, message, is_read, created_at 
          FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(['success' => true, 'notifications' => $notifications]);

$conn->close();
?>