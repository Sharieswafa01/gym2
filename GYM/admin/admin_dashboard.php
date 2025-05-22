<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css" />
    <link rel="stylesheet" href="css/update_admin_profile.css" />
    <style>
        .attendance-tracking {
            margin-top: 30px;
            background: #ffffff10;
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .attendance-tracking h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: rgb(14, 13, 13);
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
        }

        table {
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

        table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            color: #e0e0e0;
            border-bottom: 1px solid #444;
        }

        table td {
            padding: 14px 12px;
            font-size: 0.95rem;
            border-bottom: 1px solid #333;
            transition: background 0.3s;
        }

        table tbody tr:hover {
            background-color: #2e2e2e;
        }

        table tbody tr:nth-child(even) {
            background-color: #222;
        }

        .status-login {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-logout {
            color: #F44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar open" id="sidebar">
            <div class="logo">
                <h2>Gym Admin</h2>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="attendance_tracking.php">  Attendance Tracking</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="manage_equipment.php">Equipment</a></li>
                    <li><a href="announcement.php">Announcement</a></li>
                </ul>
            </nav>

            <div class="sidebar-logout">
                <a href="admin_logout.php">Logout</a>
            </div>
        </aside>

        <!-- Hamburger Icon -->
        <button class="sidebar-toggle" id="hamburger-icon">☰</button>

        <!-- Profile Icon -->
        <div class="profile-icon" onclick="toggleProfileModal()">👤</div>

        <!-- Profile Modal -->
        <div class="profile-modal" id="profileModal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="toggleProfileModal()">&times;</span>
                <h2>Edit Profile</h2>
                <form action="update_admin_profile.php" method="POST">
                    <label for="admin_name">Name:</label>
                    <input type="text" id="admin_name" name="admin_name" required />

                    <label for="admin_email">Email:</label>
                    <input type="email" id="admin_email" name="admin_email" required />

                    <label for="admin_password">New Password:</label>
                    <input type="password" id="admin_password" name="admin_password" required />

                    <button type="submit">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Welcome to Admin Dashboard</h1>
            </header>

            <section class="dashboard-cards">
                <div class="card">
                    <h3>Attendance Tracking</h3>
                    <p>Track user attendance and gym check-ins.</p>
                    <a href="attendance_tracking.php" class="card-btn">View Attendance</a>
                </div>

                <div class="card">
                    <h3>Manage Users</h3>
                    <p>View and manage all users of the gym.</p>
                    <a href="manage_users.php" class="card-btn">Go to Users</a>
                </div>

                <div class="card">
                    <h3>Equipment</h3>
                    <p>View and manage the gym equipment.</p>
                    <a href="manage_equipment.php" class="card-btn">Go to Equipment</a>
                </div>
            </section>

            <!-- Attendance Tracking Table -->
            <section class="attendance-tracking">
                <h2>Today's Attendance Records</h2>

                <?php
                include('../user/db_connection.php'); // Adjust the path as needed

                if (!isset($conn)) {
                    die("Database connection not established.");
                }

                // Fetch today's attendance records ordered by timestamp descending (newest first)
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
                    WHERE DATE(a.timestamp) = CURDATE()
                    ORDER BY a.timestamp DESC
                ";

                $result = $conn->query($query);

                $rows = [];
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                }
                $total = count($rows);
                ?>

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
                        <?php if ($total > 0): ?>
                            <?php foreach ($rows as $index => $row): ?>
                                <tr>
                                    <td><?= $total - $index ?></td> <!-- Reverse numbering: 1 at bottom -->
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                    <td class="<?= $row['status'] === 'Login' ? 'status-login' : 'status-logout' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </td>
                                    <td><?= $row['login_time'] ? htmlspecialchars($row['login_time']) : '—' ?></td>
                                    <td><?= $row['logout_time'] ? htmlspecialchars($row['logout_time']) : '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No attendance records found for today.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const hamburgerIcon = document.getElementById('hamburger-icon');

        hamburgerIcon.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        function toggleProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
