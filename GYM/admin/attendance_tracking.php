<?php
session_start();
include('../user/db_connection.php'); // Adjusted DB path

if (!isset($conn)) {
    die("Database connection not established.");
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$today = date('Y-m-d');
$viewAll = isset($_GET['view']) && $_GET['view'] === 'all';

// Base query for today's attendance ordered by newest first
$query = "
    SELECT 
        a.id, 
        CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
        u.role, 
        a.status, 
        a.login_time, 
        a.logout_time 
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE DATE(a.timestamp) = ?
    ORDER BY a.timestamp DESC
";

if (!$viewAll) {
    $query .= " LIMIT 5";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of today's records for showing "View Full List" link and numbering
$countQuery = "SELECT COUNT(*) as total FROM attendance WHERE DATE(timestamp) = ?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("s", $today);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Tracking - Admin Panel</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Optional global styles -->
    <link rel="stylesheet" href="css/attendance_tracking.css"> <!-- External CSS file -->
    <style>
        /* Your existing styles */
        .folder-section {
            margin-top: 40px;
            padding: 20px;
            background: rgb(9, 9, 9);
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .folder-section h3 a {
            text-decoration: none;
            color: rgb(246, 247, 249);
            font-weight: bold;
        }
        .folder-section h3 a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .status-login {
            color: green;
            font-weight: bold;
        }
        .status-logout {
            color: red;
            font-weight: bold;
        }
        .container {
            padding: 20px;
        }
        .back-arrow {
            display: inline-block;
            margin: 20px;
            text-decoration: none;
            font-size: 18px;
            color: #333;
        }
        .back-arrow:hover {
            text-decoration: underline;
        }
        .view-list-link {
            margin-top: 10px;
            display: inline-block;
            font-weight: bold;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }
        .view-list-link:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Back Arrow Link -->
    <a href="admin_dashboard.php" class="back-arrow" title="Go back">&#8592; Back</a>

    <div class="container">
        <h2>Today's Attendance Tracking (<?= htmlspecialchars($today) ?>)</h2>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0): 
                    $no = $totalRecords; // Start numbering from total records count
                    while($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $no-- ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td class="<?= $row['status'] === 'Login' ? 'status-login' : 'status-logout' ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </td>
                        <td><?= $row['login_time'] ? htmlspecialchars($row['login_time']) : '—' ?></td>
                        <td><?= $row['logout_time'] ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr><td colspan="6">No attendance records for today.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!$viewAll && $totalRecords > 5): ?>
            <!-- Show View Full List link only if more than 5 records exist -->
            <a href="?view=all" class="view-list-link">View Full List (<?= $totalRecords ?> records)</a>
        <?php elseif ($viewAll): ?>
            <!-- Show Show Less link when viewing full list -->
            <a href="?" class="view-list-link">Show Less</a>
        <?php endif; ?>
    </div>

    <!-- Folder-like Attendance Record Section -->
    <div class="container folder-section">
        <h3><a href="daily_folders.php">📁 Daily Attendance Records</a></h3>
    </div>

    <div class="container folder-section">
        <h3><a href="monthly_folders.php">📁 Monthly Attendance Records</a></h3>
    </div>

    <div class="container folder-section">
        <h3><a href="yearly_folders.php">📁 Yearly Attendance Records</a></h3>
    </div>
</body>
</html>
