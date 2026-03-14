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
    $location = isset($_POST['location']) ? $_POST['location'] : 'Campus';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    
    // Create emergency complaint
    $comp_type = 'medical';
    $title = 'EMERGENCY SOS ALERT';
    $description = 'Emergency SOS triggered. Location: ' . $location;
    $priority = 'emergency';
    
    $query1 = "INSERT INTO complaints (student_id, complaint_type, title, description, location, priority) 
               VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query1);
    $stmt->bind_param("isssss", $student_id, $comp_type, $title, $description, $location, $priority);
    $stmt->execute();
    $complaint_id = $stmt->insert_id;
    
    // Create emergency alert
    $query2 = "INSERT INTO emergency_alerts (student_id, location, latitude, longitude) 
               VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query2);
    $stmt->bind_param("isdd", $student_id, $location, $latitude, $longitude);
    $stmt->execute();
    
    // Notify admins
    $admins = $conn->query("SELECT user_id FROM users WHERE user_type = 'admin'");
    while ($admin = $admins->fetch_assoc()) {
        $query3 = "INSERT INTO notifications (user_id, title, message) 
                   VALUES (?, 'EMERGENCY SOS', 'Student emergency alert triggered!')";
        $stmt = $conn->prepare($query3);
        $stmt->bind_param("i", $admin['user_id']);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Emergency alert sent']);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}

$conn->close();
?>