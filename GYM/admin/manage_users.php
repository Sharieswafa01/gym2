<?php
include('../user/db_connection.php');

// Limit number of users shown in preview
$limit = 5;

$studentsQuery = "SELECT * FROM users WHERE role = 'Student' LIMIT $limit";
$customersQuery = "SELECT * FROM users WHERE role = 'Customer' LIMIT $limit";
$facultyQuery = "SELECT * FROM users WHERE role = 'Faculty' LIMIT $limit";

$studentsResult = mysqli_query($conn, $studentsQuery);
$customersResult = mysqli_query($conn, $customersQuery);
$facultyResult = mysqli_query($conn, $facultyQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="css/manage_users.css">
    <style>
        section {
            margin-bottom: 40px;
        }
        .user-section {
            margin-bottom: 60px;
        }
        .view-all-link {
            display: block;
            margin-top: 10px;
            text-align: right;
        }
        table td a {
            color: #090909;
            text-decoration: none;
            padding: 8px 15px;
            border: 2px solid #891f1f;
            border-radius: 5px;
            margin-right: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: inline-block;
            font-weight: 600;
            font-size: 0.9rem;
            user-select: none;
        }
        table td a:hover {
            background-color: #b92929;
            color: white;
        }

        /* Back arrow button */
        .back-arrow {
            position: absolute;
            top: 20px;
            left: 30px; /* Left side */
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
            background-color: rgb(1, 255, 153);
            color: #000;
        }

        /* Center and style the h1 */
        h1 {
            text-align: center;
            color: black;
            margin-top: 60px; /* adjusted to leave space for back button */
            margin-bottom: 30px;
            font-size: 2rem;
        }
    </style>
</head>
<body>

    <a href="admin_dashboard.php" class="back-arrow">← Back</a>

    <h1>Manage Users</h1>

    <main>
        <!-- Students -->
        <section class="user-section">
            <h2>Students</h2>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; while ($user = mysqli_fetch_assoc($studentsResult)) : ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                            <td><?= htmlspecialchars($user['gender']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['course']) ?></td>
                            <td><?= htmlspecialchars($user['section']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= urlencode($user['id']) ?>">Edit</a>
                                <a href="delete_user.php?id=<?= urlencode($user['id']) ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_students.php">View All Students →</a>
        </section>

        <!-- Customers -->
        <section class="user-section">
            <h2>Customers</h2>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Payment Plan</th>
                        <th>Services</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; while ($user = mysqli_fetch_assoc($customersResult)) : ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                            <td><?= htmlspecialchars($user['gender']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['payment_plan']) ?></td>
                            <td><?= htmlspecialchars($user['services']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= urlencode($user['id']) ?>">Edit</a>
                                <a href="delete_user.php?id=<?= urlencode($user['id']) ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_customers.php">View All Customers →</a>
        </section>

        <!-- Faculty -->
        <section class="user-section">
            <h2>Faculty</h2>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Faculty ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Faculty Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; while ($user = mysqli_fetch_assoc($facultyResult)) : ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($user['faculty_id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                            <td><?= htmlspecialchars($user['gender']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['faculty_dept']) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= urlencode($user['id']) ?>">Edit</a>
                                <a href="delete_user.php?id=<?= urlencode($user['id']) ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a class="view-all-link" href="view_faculty.php">View All Faculty →</a>
        </section>
    </main>

    
</body>
</html>
