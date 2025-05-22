<?php
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'gym_management');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the admin exists
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = $admin['role'];

            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "No admin found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>

        <?php if (!empty($error_message)): ?>
            <div class="notification error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
            <div class="notification success">Sign-up successful! Please log in.</div>
        <?php endif; ?>

        <form action="admin_login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Login">
        </form>

        <p><a href="admin_signup.php">        </a></p>
    </div>
</body>
</html>
