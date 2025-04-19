<?php
require_once 'cors.php';

class DbConnect {
    private $server = 'localhost';
    private $dbname = 'medicare';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        try {
            $conn = new PDO("mysql:host=$this->server;dbname=$this->dbname", $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$objDb = new DbConnect();
$conn = $objDb->connect();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Fetch facilities data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT * FROM tblfacilities";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $facilities = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($facilities);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Update facilities data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    try {
        $sql = "UPDATE tblfacilities SET ICU_Beds = ?, Normal_Rooms = ?, Ambulances = ?, XRay_Rooms = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['ICU_Beds'],
            $data['Normal_Rooms'],
            $data['Ambulances'],
            $data['XRay_Rooms']
        ]);
        echo json_encode(['message' => 'Facilities updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
