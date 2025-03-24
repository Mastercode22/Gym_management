<?php
session_start();
require 'config.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle payment approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_payment'])) {
    $payment_id = $_POST['payment_id'];
    $update_sql = "UPDATE payments SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Payment Approved!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('❌ Error Approving Payment.');</script>";
    }
    $stmt->close();
}

// Handle member deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_member'])) {
    $member_id = $_POST['member_id'];

    // Delete the member's payments first
    $delete_payments_sql = "DELETE FROM payments WHERE member_id = ?";
    $stmt1 = $conn->prepare($delete_payments_sql);
    $stmt1->bind_param("i", $member_id);
    $stmt1->execute();
    $stmt1->close();

    // Now delete the member
    $delete_member_sql = "DELETE FROM members WHERE id = ?";
    $stmt2 = $conn->prepare($delete_member_sql);
    $stmt2->bind_param("i", $member_id);

    if ($stmt2->execute()) {
        echo "<script>alert('✅ Member deleted successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting member.');</script>";
    }
    $stmt2->close();
}

// Fetch all members and payment details
$sql = "SELECT members.id, members.name, members.email, members.phone, members.membership_plan, 
               payments.id AS payment_id, payments.payment_method, payments.amount, payments.status 
        FROM members
        LEFT JOIN payments ON members.id = payments.member_id
        ORDER BY members.id DESC";


$result = $conn->query($sql);

// Fetch data for analytics
$membership_data = [];
$revenue_data = [];
$growth_data = [];

// Membership Trends (Active vs. Expired)
$membership_sql = "SELECT status, COUNT(*) as count FROM payments GROUP BY status";
$membership_result = $conn->query($membership_sql);
while ($row = $membership_result->fetch_assoc()) {
    $membership_data[$row['status']] = (int)$row['count'];
}

// Revenue Breakdown (Earnings per Payment Method)
$revenue_sql = "SELECT payment_method, SUM(amount) as total FROM payments WHERE status = 'Approved' GROUP BY payment_method";
$revenue_result = $conn->query($revenue_sql);
while ($row = $revenue_result->fetch_assoc()) {
    $revenue_data[$row['payment_method']] = (float)$row['total'];
}

// Member Growth Chart (New Members per Month)
$growth_sql = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM payments WHERE status = 'Approved' GROUP BY month";
$growth_result = $conn->query($growth_sql);
while ($row = $growth_result->fetch_assoc()) {
    $growth_data[$row['month']] = (int)$row['count'];
}

// Close DB Connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
body {
    background: linear-gradient(135deg,rgb(255, 0, 21), #493240, #00dbde, #fc00ff);
    background-size: 400% 400%;
    animation: gradientBG 10s ease infinite;
    color: #fff;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
        .container {
            background: linear-gradient(to right, #0f2027, #203a43,rgb(10, 161, 17), #fc466b);
            padding: 20px;
            border-radius: 10px;
        }
        .table {
            background: white;
            color: black;
        }
        .chart-container {
            width: 30%;
            display: inline-block;
            margin: 10px;
            background: white;
            padding: 10px;
            border-radius: 10px;
        }
        .btn-sm {
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
    width: 98px;
    margin: 3px;
}
   
.header-buttons {
    display: flex;
    font-weight:bold;
    justify-content: flex-end;
    gap: 10px; 
}
  
   </style>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="header-buttons">
    <a href="index.html" class="btn btn-warning">Home Page</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

    <h2>Analytics</h2>
    <div class="chart-container">
        <canvas id="membershipChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="growthChart"></canvas>
    </div>

    <h2>Gym Members & Payments</h2>
    <table class="table">
    <thead>
        <tr>
            <th>Member ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Membership</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['membership_plan']); ?></td>
                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                <td>GHS <?php echo htmlspecialchars($row['amount']); ?></td>
                <td><?php echo $row['status'] === 'Approved' ? '✅ Approved' : '⏳ Pending'; ?></td>
                <td>
                    <!-- ✅ Approve Payment Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
                        <button type="submit" name="approve_payment" class="btn btn-success btn-sm"
                                <?php echo $row['status'] === 'Approved' ? 'disabled' : ''; ?>>
                            Approve
                        </button>
                    </form>

                    <!-- ❌ Delete Member Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="member_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_member" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this member?');">
                            Delete
                        </button>
                    </form>

                    <!-- 📄 Generate PDF Button -->
                    <form action="generate_pdf.php" method="GET" style="display:inline;">
                        <input type="hidden" name="member_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Generate PDF</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const membershipData = <?= json_encode($membership_data) ?>;
    const revenueData = <?= json_encode($revenue_data) ?>;
    const growthData = <?= json_encode($growth_data) ?>;

    new Chart(document.getElementById("membershipChart"), { type: "pie", data: { labels: Object.keys(membershipData), datasets: [{ data: Object.values(membershipData), backgroundColor: ["#dc3545", "#28a745",] }] } });
    new Chart(document.getElementById("revenueChart"), { type: "bar", data: { labels: Object.keys(revenueData), datasets: [{ data: Object.values(revenueData), backgroundColor: ["#007bff", "#ffc107", "#17a2b8"] }] } });
    new Chart(document.getElementById("growthChart"), { type: "line", data: { labels: Object.keys(growthData), datasets: [{ data: Object.values(growthData), borderColor: "#ff6384", fill: false }] } });
});
</script>

</body>
</html>
