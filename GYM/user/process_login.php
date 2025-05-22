<?php
session_start();
include 'db_connection.php'; // Make sure this path is correct
date_default_timezone_set('Asia/Manila');

// Added for debugging - REMOVE IN PRODUCTION
error_reporting(E_ALL);
ini_set('display_errors', 1);
// END Debugging


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = trim($_POST['user_id']);

    if (empty($user_id)) {
        $_SESSION['message'] = "Please enter an ID number.";
        $_SESSION['message_type'] = "error";
        header("Location: user_login.php");
        exit();
    }

    // Initialize logged users array if not set
    if (!isset($_SESSION['logged_users'])) {
        $_SESSION['logged_users'] = [];
    }

    // Check if user already logged in
    // This check is now robust, iterating through the array
    $already_logged_in = false;
    foreach ($_SESSION['logged_users'] as $logged_user_entry) {
        if ($logged_user_entry['user_id'] === $user_id) {
            $already_logged_in = true;
            break;
        }
    }

    if ($already_logged_in) {
        $_SESSION['message'] = "Already logged in.";
        $_SESSION['message_type'] = "error";
        header("Location: user_login.php");
        exit();
    }

    $query = "SELECT * FROM users WHERE student_id = ? OR customer_id = ? OR faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Prepare user info for both 'logged_users' and 'current_user_info'
        $userInfo = [
            'user_id' => $user_id, // The ID entered by the user
            'internal_id' => $user['id'], // The primary key ID from your users table
            'name' => $user['first_name'] . " " . $user['last_name'],
            'age' => $user['age'],
            'gender' => $user['gender'],
            'login_time' => date("Y-m-d h:i:s A"), // Current time in Asia/Manila
            'type' => 'Unknown',
            'course' => '',
            'section' => '',
        ];

        if ($user['student_id'] === $user_id) {
            $userInfo['type'] = 'Student';
            $userInfo['course'] = $user['course'];
            $userInfo['section'] = $user['section'];
        } elseif ($user['customer_id'] === $user_id) {
            $userInfo['type'] = 'Customer';
        } elseif ($user['faculty_id'] === $user_id) {
            $userInfo['type'] = 'Faculty';
        }

        // --- THIS IS THE CRUCIAL LINE THAT WAS MISSING FROM YOUR LAST PROVIDED CODE ---
        // Store the specific logged-in user's details for display on user_login.php
        $_SESSION['current_user_info'] = $userInfo;
        // --- END OF CRUCIAL LINE ---

        // Insert into attendance table
        $loginTime = date("Y-m-d H:i:s"); // Use H:i:s for 24-hour format for database storage
        $insert = $conn->prepare("INSERT INTO attendance (user_id, status, login_time) VALUES (?, 'Login', ?)");
        $insert->bind_param("is", $user['id'], $loginTime); // Use the internal 'id' from the users table
        $insert->execute();
        $insert->close(); // Close the insert statement

        // Add this user to the logged_users session array (for tracking all active logins)
        // Make sure you don't duplicate entries if a user logs in multiple times without logging out
        // (The 'already_logged_in' check above should handle this for the 'logged_users' array itself)
        if (!$already_logged_in) { // Only add to logged_users if they weren't already in the list
            $_SESSION['logged_users'][] = $userInfo;
        }


        $_SESSION['notification'] = "Login Successful ";
        $_SESSION['notification_type'] = "login-success";

    } else {
        $_SESSION['message'] = "Invalid ID. Please try again.";
        $_SESSION['message_type'] = "error";
    }

    $stmt->close(); // Close the select statement
    $conn->close(); // Close the database connection
    header("Location: user_login.php");
    exit();
}
?>