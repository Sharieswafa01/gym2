<?php
include('../user/db_connection.php');

$query = "SELECT * FROM users WHERE role = 'Student'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>All Students</title>
    <style>
        /* General reset and box-sizing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        a.back-link {
            align-self: flex-start;
            margin-bottom: 20px;
            color: #090909;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 18px;
            border: 2px solid #891f1f;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        a.back-link:hover {
            background-color: #b92929;
            color: white;
        }

        .container {
            background-color: white;
            width: 100%;
            max-width: 1300px;
            padding: 25px 30px 30px 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .container h3 {
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.8rem;
            color: #333;
            user-select: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1rem;
        }

        table th {
            background-color: #090909;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            user-select: none;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
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

        /* Responsive Design for Smaller Screens */
        @media (max-width: 768px) {
            .container {
                padding: 20px 15px 20px 15px;
            }

            .container h3 {
                font-size: 1.4rem;
            }

            a.back-link {
                font-size: 0.9rem;
                padding: 6px 12px;
            }

            table th, table td {
                font-size: 0.85rem;
                padding: 10px 8px;
            }

            table td a {
                font-size: 0.8rem;
                padding: 6px 12px;
                margin-bottom: 6px;
                display: block;
            }
        }
    </style>
</head>
<body>
    <a href="manage_users.php" class="back-link">← Back</a>
    <div class="container">
        <h3>Students List</h3>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Student ID</th>
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
                <?php $counter = 1; while ($user = mysqli_fetch_assoc($result)) : ?>
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
                        <a href="edit_user.php?id=<?= urlencode($user['student_id']) ?>">Edit</a>
                        <a href="delete_user.php?id=<?= urlencode($user['student_id']) ?>">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
