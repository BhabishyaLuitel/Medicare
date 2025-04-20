<?php
require_once 'cors.php';

class DbConnect {
    private $server = 'localhost';
    private $dbname = 'medicare';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        try {
            $conn = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->dbname, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$objDb = new DbConnect;
$conn = $objDb->connect();

if (isset($_GET["ID"])) {
    $ID = $_GET["ID"];
    $data1 = array();
    $sql = "SELECT * FROM tblusers WHERE ID != :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $ID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $res = array("status" => "success", "data" => $result);
        } else {
            $res = array("status" => "failed", "data" => 'No records found.');
        }
    } else {
        $res = array("status" => "failed", "data" => $stmt->errorInfo());
    }

    echo json_encode($res);
} else {
    $res = array("status" => "failed", "data" => 'Missing ID parameter.');
    echo json_encode($res);
}

$conn = null;
?>
