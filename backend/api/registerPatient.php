<?php
require_once 'cors.php';
require 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $data['FirstName'];
    $lastName = $data['LastName'];
    $email = $data['Email'];
    $password = $data['Password'];
    $userType = $data['UserType'];
    $contactNumber = $data['ContactNumber'];

    try {
        // Check if email already exists
        $checkSql = "SELECT * FROM tblusers WHERE Email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['status' => 0, 'message' => 'Email already exists']);
            exit();
        }

        // Insert new patient
        $sql = "INSERT INTO tblusers (FirstName, LastName, Email, Password, UserType, ContactNumber) 
                VALUES (:firstName, :lastName, :email, :password, :userType, :contactNumber)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':userType', $userType);
        $stmt->bindParam(':contactNumber', $contactNumber);

        if ($stmt->execute()) {
            echo json_encode(['status' => 1, 'message' => 'Patient registered successfully']);
        } else {
            echo json_encode(['status' => 0, 'message' => 'Failed to register patient']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 0, 'message' => $e->getMessage()]);
    }
}
?>