<?php
session_start();
include("../config/dbconfig.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $zodiac_sign = $_POST['zodiac_sign'] ?? '';
    
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Check if password change is requested
        $password_changed = false;
        if (!empty($new_password) && !empty($confirm_password)) {
            // Simple password validation - no current password check
            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match");
            }
            
            if (strlen($new_password) < 6) {
                throw new Exception("New password must be at least 6 characters long");
            }
            
            // Update password directly
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $pass_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $pass_stmt->bind_param("si", $hashed_password, $user_id);
            $pass_stmt->execute();
            $pass_stmt->close();
            
            $password_changed = true;
        }
        
        // Update profile information
        $update_fields = [];
        $params = [];
        $types = "";
        
        if (!empty($name)) {
            $update_fields[] = "name = ?";
            $params[] = $name;
            $types .= "s";
        }
        
        if (!empty($gender)) {
            $update_fields[] = "gender = ?";
            $params[] = $gender;
            $types .= "s";
        }
        
        if (!empty($birthdate)) {
            $update_fields[] = "birthdate = ?";
            $params[] = $birthdate;
            $types .= "s";
        }
        
        if (!empty($zodiac_sign)) {
            $update_fields[] = "zodiac_sign = ?";
            $params[] = $zodiac_sign;
            $types .= "s";
        }
        
        // Only update if there are fields to update
        if (!empty($update_fields)) {
            $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
            $params[] = $user_id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            
            if ($stmt->error) {
                throw new Exception("Database error: " . $stmt->error);
            }
            
            $stmt->close();
        }
        
        $conn->commit();
        
        // Return success response
        $response = [
            'success' => true,
            'message' => $password_changed ? 'Profile and password updated successfully!' : 'Profile updated successfully!',
            'data' => [
                'name' => $name,
                'gender' => $gender,
                'birthdate' => $birthdate,
                'zodiac_sign' => $zodiac_sign
            ]
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>