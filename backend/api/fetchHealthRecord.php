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

$UserID = isset($_GET['UserID']) ? $_GET['UserID'] : '';

if (empty($UserID)) {
    echo json_encode(['error' => 'UserID is missing.']);
    exit;
}

$sql = "SELECT * FROM tblpersonalrecords WHERE UserID = :UserID";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':UserID', $UserID, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'No health record found.']);
}
?>
