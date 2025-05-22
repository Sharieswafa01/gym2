<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../user/db_connection.php'); // Adjust if needed

$query = "SELECT * FROM equipment ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Equipment</title>
    <link rel="stylesheet" href="css/admin_dashboard.css" />
    <style>
        .equipment-section {
            margin-top: 30px;
            background: #ffffff10;
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }

        .equipment-section h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
            color: rgb(14, 13, 13);
        }

        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1c1c1c;
            color: #f5f5f5;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background-color: #272727;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            font-size: 0.95rem;
            border-bottom: 1px solid #333;
        }

        tbody tr:hover {
            background-color: #2e2e2e;
        }

        .btn-container {
            margin-top: 15px;
        }

        .btn {
            display: inline-block;
            margin-right: 10px;
            background: #4CAF50;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background: #45a049;
        }

        .no-records {
            color: #ddd;
            padding: 20px 0;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar open" id="sidebar">
        <div class="logo"><h2>Gym Admin</h2></div>
        <nav class="nav">
            <ul>
                <li><a href="attendance_tracking.php">🕒 Attendance Tracking</a></li>
                <li><a href="manage_users.php">👥 Manage Users</a></li>
                <li><a href="manage_equipment.php">🏋️ Equipment</a></li>
                <li><a href="announcement.php">📢 Announcement</a></li>
            </ul>
        </nav>
        <div class="sidebar-logout">
            <a href="admin_logout.php">🚪 Logout</a>
        </div>
    </aside>

    <button class="sidebar-toggle" id="hamburger-icon">☰</button>

    <main class="main-content">
        <header class="header">
            <h1>Manage Gym Equipment</h1>
        </header>

        <section class="equipment-section">
            <div class="btn-container">
                <a href="add_equipment.php" class="btn">➕ Add Equipment</a>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="equipment-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Equipment Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <a href="update_equipment.php?id=<?= $row['id'] ?>" class="btn">✏️ Edit</a>
                                    <a href="delete_equipment.php?id=<?= $row['id'] ?>" class="btn" style="background:#f44336;" onclick="return confirm('Are you sure you want to delete this equipment?');">🗑️ Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-records">No equipment records found.</div>
            <?php endif; ?>
        </section>
    </main>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const hamburgerIcon = document.getElementById('hamburger-icon');

    hamburgerIcon.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
</script>
</body>
</html>
