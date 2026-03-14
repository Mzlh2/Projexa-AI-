<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $student_id = $_SESSION['user_id'];
    $complaint_type = isset($_POST['complaint_type']) ? trim($_POST['complaint_type']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    
    if (empty($complaint_type) || empty($title) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'All fields required']);
        exit();
    }
    
    $query = "INSERT INTO complaints (student_id, complaint_type, title, description, location) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $student_id, $complaint_type, $title, $description, $location);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Complaint submitted',
            'complaint_id' => $stmt->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}

$conn->close();
?>