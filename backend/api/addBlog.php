<?php
require_once 'cors.php';

class DbConnect {
    private $server = 'localhost';
    private $dbname = 'medicare';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        try {
            $conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbname, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            echo "Database Error: " . $e->getMessage();
            return null;
        }
    }
}

// Create database connection
$objDb = new DbConnect;
$conn = $objDb->connect();

// Check if connection is successful
if (!$conn) {
    // Connection failed, handle error or return appropriate response
    die("Database connection failed.");
}

// Logic to handle POST request for adding a new blog
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from request body (assuming JSON format)
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate data (e.g., check if all required fields are present)

    // Retrieve userId from request body
    $userId = $data['userId'];

    // Insert the blog into the database
    try {
        $stmt = $conn->prepare("INSERT INTO tblBlogs (Title, Content, UserID) VALUES (:title, :content, :userId)");
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        // Return success response
        echo json_encode(array("message" => "Blog added successfully"));
    } catch (PDOException $e) {
        // Handle database error
        echo json_encode(array("error" => "Database Error: " . $e->getMessage()));
    }
}
?>
