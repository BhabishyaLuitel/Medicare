<?php
require_once 'cors.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DbConnect {
    private $server = 'localhost';
    private $dbname = 'medicare';
    private $user = 'root';
    private $pass = '123Aayam123';

    public function connect() {
        try {
            $conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbname, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            echo "Database Error: " . $e->getMessage();
            exit();
        }
    }
}

$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM tblusers";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = $users ? $users : [];
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = $users ? $users : [];
        }
        echo json_encode($response);
        break;

    case "POST":
        $user = json_decode(file_get_contents('php://input'));

        $checkEmailQuery = "SELECT COUNT(*) as emailCount FROM tblusers WHERE Email = :emailid";
        $checkIdQuery = "SELECT COUNT(*) as idCount FROM tblusers WHERE ID = :userId";
        $stmtCheckEmail = $conn->prepare($checkEmailQuery);
        $stmtCheckEmail->bindParam(':emailid', $user->emailid);
        $stmtCheckEmail->execute();
        $emailCount = $stmtCheckEmail->fetch(PDO::FETCH_ASSOC);

        $stmtCheckId = $conn->prepare($checkIdQuery);
        $stmtCheckId->bindParam(':userId', $user->userId);
        $stmtCheckId->execute();
        $idCount = $stmtCheckId->fetch(PDO::FETCH_ASSOC);

        if ($emailCount['emailCount'] > 0 || $idCount['idCount'] > 0) {
            $response = ['status' => 0, 'message' => 'Email or ID already exists'];
        } else {
            $code = rand(999999, 111111);
            $status = "notverified";

            $sql = "INSERT INTO tblusers(FirstName, LastName, Email, ID, ContactNumber, Password, UserType, code, status) VALUES(:firstname, :lastname, :emailid, :userId, :phone, :password, :userType, :code, :status)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':firstname', $user->firstname);
            $stmt->bindParam(':lastname', $user->lastname);
            $stmt->bindParam(':emailid', $user->emailid);
            $stmt->bindParam(':userId', $user->userId);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':userType', $user->userType);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'bhabishyaluitel@gmail.com'; // Fixed Gmail account
                    $mail->Password = 'qwse glwu bhpx vyvz'; // Replace with App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    // $mail->SMTPDebug = 2; // Enable verbose debug output

                    // Recipients
                    $mail->setFrom('bhabishyaluitel@gmail.com', 'Medicare System');
                    $mail->addAddress($user->emailid);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body = "
                        <h2>Welcome to Medicare!</h2>
                        <p>Thank you for registering. Your verification code is:</p>
                        <h1 style='color: #4CAF50;'>{$code}</h1>
                        <p>Please use this code to verify your email address.</p>
                    ";

                    $mail->send();
                    
                    $info = "We've sent a verification code to your email - " . $user->emailid;
                    $_SESSION['info'] = $info;
                    $_SESSION['email'] = $user->emailid;
                    $_SESSION['password'] = $user->password;
                    $response = ['status' => 1, 'message' => 'Record created successfully and verification email sent'];
                } catch (Exception $e) {
                    $errorMessage = "Failed to send verification email. ";
                    $errorMessage .= "Error details: " . $e->getMessage();
                    $errorMessage .= "\nPlease check your email configuration.";
                    $response = ['status' => 0, 'message' => $errorMessage];
                }
            } else {
                $response = ['status' => 0, 'message' => 'Failed to insert data into the database'];
            }
        }

        echo json_encode($response);
        break;

    case "LOGIN":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "SELECT * from tblusers WHERE Email = :emailid AND Password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':emailid', $user->emailid);
        $stmt->bindParam(':password', $user->password);
        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record found successfully'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to find record'];
        }
        echo json_encode($response);
        break;

    default:
        $response = ['status' => 0, 'message' => 'Invalid request'];
        echo json_encode($response);
        break;
}
?>
