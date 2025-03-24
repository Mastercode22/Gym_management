<?php
ob_start(); // Start output buffering to prevent TCPDF errors

require('tcpdf/tcpdf.php'); // Correct path to the TCPDF library
require 'config.php'; // Your database connection

if (!isset($_GET['member_id'])) {
    die("❌ No member ID provided.");
}

$member_id = $_GET['member_id'];

// ✅ Correct SQL Query
$sql = "SELECT 
            members.id, 
            members.name, 
            members.email, 
            members.phone, 
            payments.amount, 
            payments.created_at AS payment_date, 
            payments.payment_method, 
            payments.status
        FROM members
        JOIN payments ON members.id = payments.member_id
        WHERE members.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("❌ Member not found or no payment records.");
}

// ✅ Assign values safely
$member_name = $row['name'];
$member_email = $row['email'];
$member_phone = $row['phone'];
$amount = number_format($row['amount'], 2); // Format amount
$payment_date = $row['payment_date'];
$payment_method = !empty($row['payment_method']) ? $row['payment_method'] : 'N/A';
$status = !empty($row['status']) ? $row['status'] : 'N/A';

// ✅ Generate PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, "Gym Membership Payment Report", 0, 1, 'C');
$pdf->Ln(10); // Line break

// ✅ Member Details
$pdf->Cell(0, 10, "Name: $member_name", 0, 1);
$pdf->Cell(0, 10, "Email: $member_email", 0, 1);
$pdf->Cell(0, 10, "Phone: $member_phone", 0, 1);
$pdf->Cell(0, 10, "Payment Method: $payment_method", 0, 1);
$pdf->Cell(0, 10, "Amount Paid: GHS $amount", 0, 1);
$pdf->Cell(0, 10, "Status: $status", 0, 1);
$pdf->Cell(0, 10, "Payment Date: $payment_date", 0, 1);

ob_end_clean(); // Clear the output buffer to fix TCPDF issues
$pdf->Output('member_report.pdf', 'I');
?>
