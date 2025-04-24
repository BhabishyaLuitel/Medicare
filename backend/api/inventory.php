<?php
require_once 'cors.php';
require 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all inventory items
    $sql = "SELECT * FROM inventory ORDER BY CreatedAt DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($inventory);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new inventory item
    $medicineName = $data['MedicineName'];
    $quantity = $data['Quantity'];
    $expiryDate = $data['ExpiryDate'];
    $price = $data['Price'];

    $sql = "INSERT INTO inventory (MedicineName, Quantity, ExpiryDate, Price) VALUES (:medicineName, :quantity, :expiryDate, :price)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':medicineName', $medicineName);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':expiryDate', $expiryDate);
    $stmt->bindParam(':price', $price);

    if ($stmt->execute()) {
        echo json_encode(['status' => 1, 'message' => 'Item added successfully']);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Failed to add item']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update an inventory item
    $id = $data['ID'];
    $medicineName = $data['MedicineName'];
    $quantity = $data['Quantity'];
    $expiryDate = $data['ExpiryDate'];
    $price = $data['Price'];

    $sql = "UPDATE inventory SET MedicineName = :medicineName, Quantity = :quantity, ExpiryDate = :expiryDate, Price = :price WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':medicineName', $medicineName);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':expiryDate', $expiryDate);
    $stmt->bindParam(':price', $price);

    if ($stmt->execute()) {
        echo json_encode(['status' => 1, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Failed to update item']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete an inventory item
    $id = $data['ID'];

    $sql = "DELETE FROM inventory WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 1, 'message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Failed to delete item']);
    }
}
?>