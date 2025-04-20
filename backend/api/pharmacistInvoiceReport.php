<?php
require_once 'cors.php';
require 'fpdf.php'; // Include FPDF library

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
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

$objDb = new DbConnect();
$conn = $objDb->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $patientId = $data['patientId'];

    try {
        // Fetch patient and prescription details from tblusers and tblprescriptions
        $sql = "SELECT u.FirstName, u.LastName, pr.name AS PrescriptionName, pr.medicines, pr.pharmacist, pr.prescribedBy 
                FROM tblusers u 
                JOIN tblprescriptions pr ON u.ID = pr.user_id 
                WHERE u.ID = :patientId AND u.UserType = 'Patient'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patientData) {
            echo json_encode(['error' => 'No data found for the given patient ID']);
            exit();
        }

        // Generate PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add title
        $pdf->Cell(0, 10, 'Patient Invoice', 0, 1, 'C');
        $pdf->Ln(10);

        // Add patient details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Patient Name: ' . $patientData['FirstName'] . ' ' . $patientData['LastName'], 0, 1);
        $pdf->Cell(0, 10, 'Prescription Name: ' . $patientData['PrescriptionName'], 0, 1);
        $pdf->Cell(0, 10, 'Prescribed By: ' . $patientData['prescribedBy'], 0, 1);
        $pdf->Cell(0, 10, 'Pharmacist: ' . $patientData['pharmacist'], 0, 1);
        $pdf->Ln(10);

        // Add medicines
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Medicines:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $medicines = explode(',', $patientData['medicines']);
        foreach ($medicines as $medicine) {
            $pdf->Cell(0, 10, '- ' . trim($medicine), 0, 1);
        }

        // Output the PDF
        $fileName = 'invoice_' . $patientId . '.pdf';
        $filePath = __DIR__ . '/' . $fileName; // Save the file in the same directory
        $pdf->Output('F', $filePath); // Save the PDF to a file

        echo json_encode(['message' => 'PDF generated successfully', 'file' => $fileName]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>