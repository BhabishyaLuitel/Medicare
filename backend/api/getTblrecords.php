<?php
require_once 'cors.php';
require 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Fetch all patients and their personal records
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT 
                    u.ID AS UserID, 
                    u.FirstName, 
                    u.LastName, 
                    pr.BP, 
                    pr.Diabetes, 
                    pr.HeartHealthIssues, 
                    pr.Arthritis, 
                    pr.Allergies, 
                    pr.OtherIssues 
                FROM tblusers u
                LEFT JOIN tblpersonalrecords pr ON u.ID = pr.UserID
                WHERE u.UserType = 'Patient'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendJSON($records);
    } catch (Exception $e) {
        sendJSON(['error' => $e->getMessage()], 500);
    }
}

$conn = null;
?>
