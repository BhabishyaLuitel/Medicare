<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'cors.php';
require 'vendor/autoload.php';
require 'fpdf.php'; // Include FPDF library

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
        }
    }
}

$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $data = array();
    
        // Total count of UserType = Pharmacist from tblusers
        $sqlPharmacists = "SELECT COUNT(UserType) AS TotalPharmacistCount FROM tblusers WHERE UserType = 'Pharmacist'";
        $stmtPharmacists = $conn->prepare($sqlPharmacists);
        $stmtPharmacists->execute();
        $pharmacistCount = $stmtPharmacists->fetch(PDO::FETCH_ASSOC);
        $data['TotalPharmacistCount'] = $pharmacistCount['TotalPharmacistCount'];
    
        // Total count of UserType = Patient from tblusers
        $sqlPatients = "SELECT COUNT(UserType) AS TotalPatientCount FROM tblusers WHERE UserType = 'Patient'";
        $stmtPatients = $conn->prepare($sqlPatients);
        $stmtPatients->execute();
        $PatientCount = $stmtPatients->fetch(PDO::FETCH_ASSOC);
        $data['TotalPatientCount'] = $PatientCount['TotalPatientCount'];

        // Total count of UserType = HealthAdminstrator from tblusers
        $sqlHealthAdminstrators = "SELECT COUNT(UserType) AS TotalHealthAdminstratorCount FROM tblusers WHERE UserType = 'HealthAdminstrator'";
        $stmtHealthAdminstrators = $conn->prepare($sqlHealthAdminstrators);
        $stmtHealthAdminstrators->execute();
        $HealthAdminstratorCount = $stmtHealthAdminstrators->fetch(PDO::FETCH_ASSOC);
        $data['TotalHealthAdminstratorCount'] = $HealthAdminstratorCount['TotalHealthAdminstratorCount'];
    
         // Total count of UserType = HealthcareProvider from tblusers
        $sqlHealthcareProviders = "SELECT COUNT(UserType) AS TotalHealthcareProviderCount FROM tblusers WHERE UserType = 'HealthcareProvider'";
        $stmtHealthcareProviders = $conn->prepare($sqlHealthcareProviders);
        $stmtHealthcareProviders->execute();
        $HealthcareProviderCount = $stmtHealthcareProviders->fetch(PDO::FETCH_ASSOC);
        $data['TotalHealthcareProviderCount'] = $HealthcareProviderCount['TotalHealthcareProviderCount'];

        echo json_encode($data);
        break;

    case "POST":
        // Generate PDF report
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add title
        $pdf->Cell(0, 10, 'Management System Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Fetch data for the report
        $data = array();

        $sqlPharmacists = "SELECT COUNT(UserType) AS TotalPharmacistCount FROM tblusers WHERE UserType = 'Pharmacist'";
        $stmtPharmacists = $conn->prepare($sqlPharmacists);
        $stmtPharmacists->execute();
        $pharmacistCount = $stmtPharmacists->fetch(PDO::FETCH_ASSOC);
        $data['TotalPharmacistCount'] = $pharmacistCount['TotalPharmacistCount'];

        $sqlPatients = "SELECT COUNT(UserType) AS TotalPatientCount FROM tblusers WHERE UserType = 'Patient'";
        $stmtPatients = $conn->prepare($sqlPatients);
        $stmtPatients->execute();
        $PatientCount = $stmtPatients->fetch(PDO::FETCH_ASSOC);
        $data['TotalPatientCount'] = $PatientCount['TotalPatientCount'];

        $sqlHealthAdminstrators = "SELECT COUNT(UserType) AS TotalHealthAdminstratorCount FROM tblusers WHERE UserType = 'HealthAdminstrator'";
        $stmtHealthAdminstrators = $conn->prepare($sqlHealthAdminstrators);
        $stmtHealthAdminstrators->execute();
        $HealthAdminstratorCount = $stmtHealthAdminstrators->fetch(PDO::FETCH_ASSOC);
        $data['TotalHealthAdminstratorCount'] = $HealthAdminstratorCount['TotalHealthAdminstratorCount'];

        $sqlHealthcareProviders = "SELECT COUNT(UserType) AS TotalHealthcareProviderCount FROM tblusers WHERE UserType = 'HealthcareProvider'";
        $stmtHealthcareProviders = $conn->prepare($sqlHealthcareProviders);
        $stmtHealthcareProviders->execute();
        $HealthcareProviderCount = $stmtHealthcareProviders->fetch(PDO::FETCH_ASSOC);
        $data['TotalHealthcareProviderCount'] = $HealthcareProviderCount['TotalHealthcareProviderCount'];

        // Add data to the PDF
        foreach ($data as $key => $value) {
            $pdf->Cell(0, 10, "$key: $value", 0, 1);
        }

        // Output the PDF
        $pdf->Output('F', 'report.pdf'); // Save the PDF to a file
        echo json_encode(['message' => 'PDF report generated successfully', 'file' => 'report.pdf']);
        break;
}