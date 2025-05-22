<?php
session_start();
include 'db_connection.php'; // Make sure this path is correct
date_default_timezone_set('Asia/Manila');

// Added for debugging - REMOVE IN PRODUCTION
error_reporting(E_ALL);
ini_set('display_errors', 1);
// END Debugging

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

$user_id = trim($_POST['user_id']);

if (empty($user_id)) {
    $_SESSION['message'] = "Please enter an ID number.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

if (!isset($_SESSION['logged_users']) || empty($_SESSION['logged_users'])) {
    $_SESSION['message'] = "No users are currently logged in for logout processing.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

// Find user in logged_users array by their 'user_id' (student_id, customer_id, or faculty_id)
$index = null;
$loggedOutUser = null; // Variable to store the user's data before unsetting
foreach ($_SESSION['logged_users'] as $key => $loggedUser) {
    if ($loggedUser['user_id'] === $user_id) {
        $index = $key;
        $loggedOutUser = $loggedUser; // Capture the user's data
        break;
    }
}

if ($index === null) {
    $_SESSION['message'] = "User with this ID is not currently marked as logged in.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

// Ensure $loggedOutUser is set before proceeding
if ($loggedOutUser === null) {
    $_SESSION['message'] = "Error: User data not found for logout.";
    $_SESSION['message_type'] = "error";
    header("Location: user_login.php");
    exit();
}

$internalId = $loggedOutUser['internal_id'];
$logoutTime = date("Y-m-d H:i:s"); // 24-hour format for DB storage

// Update attendance record: set logout time for the latest login session that doesn't have a logout time
// Use a subquery or JOIN if user can log in multiple times without logging out
$update = $conn->prepare("UPDATE attendance SET status = 'Logout', logout_time = ? WHERE user_id = ? AND logout_time IS NULL ORDER BY id DESC LIMIT 1");
if ($update) {
    $update->bind_param("si", $logoutTime, $internalId);
    $update->execute();
    $update->close();
} else {
    // Handle prepare error
    error_log("Failed to prepare logout attendance update: " . $conn->error);
}


// --- CRUCIAL CHANGE: Store the logged out user's complete info for display ---
// We use a new session variable to show the last logged out user's info
$_SESSION['last_logged_out_user_info'] = $loggedOutUser;
$_SESSION['last_logged_out_user_info']['logout_time'] = date("Y-m-d h:i:s A"); // Store formatted logout time

// --- ALSO CRUCIAL: Remove the current user's active login state ---
// This ensures user_login.php correctly shows "Please login" or the logout info
unset($_SESSION['current_user_info']);

// Remove user from logged_users array
unset($_SESSION['logged_users'][$index]);

// Re-index the array so it stays consistent
$_SESSION['logged_users'] = array_values($_SESSION['logged_users']);

$_SESSION['notification'] = "Logout Successful ";
$_SESSION['notification_type'] = "logout-success";

$conn->close();
header("Location: user_login.php");
exit();
?>