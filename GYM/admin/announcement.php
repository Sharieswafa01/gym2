<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('../user/db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if ($title && $message) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, message, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $title, $message);
        $stmt->execute();

        // Redirect to prevent resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch announcements
$announcements = [];
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Announcements</title>
    <link rel="stylesheet" href="css/admin_dashboard.css" />
    <style>
        /* Clean full-width layout without sidebar */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            overflow: hidden;
        }

        /* Back arrow button */
        .back-arrow {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 2rem;
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
            z-index: 1000;
        }
        .back-arrow:hover {
            color: #388e3c;
        }

        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding: 40px 60px;
            box-sizing: border-box;
            background-color: #fff;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
        }

        header.header h1 {
            margin-top: 0;
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 40px;
            text-align: center;
        }

        .announcement-section {
            background: #ffffff10;
            backdrop-filter: blur(8px);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 50px;
        }

        .announcement-section h2 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #111;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
        }

        .announcement-form input,
        .announcement-form textarea {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1.1rem;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .announcement-form input:focus,
        .announcement-form textarea:focus {
            border-color: #4caf50;
            outline: none;
        }

        .announcement-form button {
            width: 100%;
            padding: 14px;
            font-size: 1.2rem;
            border: none;
            background: #4caf50;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .announcement-form button:hover:not(:disabled) {
            background: #45a049;
        }

        .announcement-form button:disabled {
            background: #a5d6a7;
            cursor: not-allowed;
        }

        .announcement-list h2 {
            color: #111;
            margin-bottom: 25px;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
            text-align: center;
        }

        .announcement-card {
            background-color: #1c1c1c;
            color: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }

        .announcement-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .announcement-card small {
            display: block;
            color: #bbb;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <a href="admin_dashboard.php" class="back-arrow" title="Back">&#8592;</a>

    <main class="main-content">
        <header class="header">
            <h1>Manage Announcements</h1>
        </header>

        <section class="announcement-section">
            <h2>Create New Announcement</h2>
            <form class="announcement-form" method="POST" autocomplete="off">
                <input type="text" name="title" placeholder="Announcement Title" required />
                <textarea name="message" rows="5" placeholder="Announcement Message" required></textarea>
                <button type="submit">Post Announcement</button>
            </form>
        </section>

        <section class="announcement-list">
            <h2>Posted Announcements</h2>
            <?php if (!empty($announcements)): ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-card">
                        <h3><?= htmlspecialchars($announcement['title']) ?></h3>
                        <small>Posted on: <?= date("F j, Y - g:i A", strtotime($announcement['created_at'])) ?></small>
                        <p><?= nl2br(htmlspecialchars($announcement['message'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#666;">No announcements posted yet.</p>
            <?php endif; ?>
        </section>
    </main>

    <script>
      const form = document.querySelector('.announcement-form');
      form.addEventListener('submit', () => {
        // Disable submit button on form submit to prevent double posts
        form.querySelector('button[type="submit"]').disabled = true;
      });
    </script>
</body>
</html>
