<?php
session_start();
include('../user/db_connection.php'); // Ensure this path is correct

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch distinct login dates from the attendance table
$query = "SELECT DISTINCT DATE(login_time) AS record_date FROM attendance ORDER BY record_date DESC";
$result = $conn->query($query);

// Check for database errors
if (!$result) {
    die("Error fetching records: " . $conn->error);
}

// Get the current month name
$current_month = date("F"); // May, June, etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance Folders</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 40px auto;
            max-width: 900px;
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .folder-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
        }

        .folder-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: 0.3s;
            width: 60px;
            text-align: center;
        }

        .folder-link:hover {
            background-color: #e0e0e0;
        }

        .back-arrow {
    position: absolute;
    top: 20px;
    left: 30px; /* LEFT SIDE instead of right */
    font-size: 16px;
    text-decoration: none;
    background-color: rgba(255, 255, 255, 0.9);
    color: #000;
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-arrow:hover {
    background-color: #00ff99;
    color: #000;
}

        p {
            font-size: 16px;
            color: #555;
        }

        .month-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <a href="attendance_tracking.php" class="back-arrow">&#8592; Back</a>
    <div class="container">
        <h2>Daily Attendance Records</h2>
        <p class="month-title"><?= $current_month ?></p>

        <div class="folder-list">
            <?php for ($day = 1; $day <= 31; $day++): ?>
                <a class="folder-link" href="daily_record_view.php?date=<?= date('Y-m-d', strtotime("2025-$current_month-$day")) ?>">
                    📁 <?= $day ?>
                </a>
            <?php endfor; ?>
        </div>

        <div class="folder-list">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // Format date for display (e.g., "May 14, 2025")
                        $date = $row['record_date'];
                        $formatted = date("F j, Y", strtotime($date));
                    ?>
                    <!-- Link to the daily attendance page for the specific date -->
                    
                <?php endwhile; ?>
            <?php else: ?>
                <p>No attendance records found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
