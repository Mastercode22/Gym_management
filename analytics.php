<?php
session_start();
require 'config.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Get Membership Trends
$membership_trends_query = "SELECT status, COUNT(*) as count FROM payments GROUP BY status";
$membership_trends_result = $conn->query($membership_trends_query);
$membership_data = [];
while ($row = $membership_trends_result->fetch_assoc()) {
    $membership_data[$row['status']] = $row['count'];
}

// Get Revenue Breakdown
$revenue_query = "SELECT payment_method, SUM(amount) as total FROM payments WHERE status='Approved' GROUP BY payment_method";
$revenue_result = $conn->query($revenue_query);
$revenue_data = [];
while ($row = $revenue_result->fetch_assoc()) {
    $revenue_data[$row['payment_method']] = $row['total'];
}

// Get Monthly Member Growth
$growth_query = "SELECT MONTH(created_at) as month, COUNT(*) as total FROM members GROUP BY MONTH(created_at)";
$growth_result = $conn->query($growth_query);
$growth_data = [];
while ($row = $growth_result->fetch_assoc()) {
    $growth_data[$row['month']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Dashboard Analytics</h2>
        <canvas id="membershipChart"></canvas>
        <canvas id="revenueChart"></canvas>
        <canvas id="growthChart"></canvas>
    </div>

    <script>
        // Membership Trends Chart
        var ctx1 = document.getElementById('membershipChart').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Pending'],
                datasets: [{
                    data: [<?= $membership_data['Approved'] ?? 0 ?>, <?= $membership_data['Pending'] ?? 0 ?>],
                    backgroundColor: ['green', 'orange']
                }]
            }
        });

        // Revenue Breakdown Chart
        var ctx2 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($revenue_data)) ?>,
                datasets: [{
                    label: 'Total Revenue (GHS)',
                    data: <?= json_encode(array_values($revenue_data)) ?>,
                    backgroundColor: 'blue'
                }]
            }
        });

        // Monthly Growth Chart
        var ctx3 = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($growth_data)) ?>,
                datasets: [{
                    label: 'New Members',
                    data: <?= json_encode(array_values($growth_data)) ?>,
                    borderColor: 'red',
                    fill: false
                }]
            }
        });
    </script>
</body>
</html>
