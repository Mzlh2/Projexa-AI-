<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $complaint_id = isset($_POST['complaint_id']) ? $_POST['complaint_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if (empty($complaint_id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Missing fields']);
        exit();
    }
    
    $query = "UPDATE complaints SET status = ? WHERE complaint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $complaint_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}

$conn->close();
?>