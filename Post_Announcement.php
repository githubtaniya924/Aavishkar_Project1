<?php
include 'HR_Connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: LoginAsHR.html");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subject']) && isset($_POST['content'])) {
        $subject = $_POST['subject'];
        $content = $_POST['content'];

        $file_name = "";

        // Handle file upload (image/video)
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_name = $_FILES['file']['tmp_name'];
            $file_name = $_FILES['file']['name'];
            $file_type = $_FILES['file']['type'];
            $file_size = $_FILES['file']['size'];

            // Define allowed file types (e.g., image, video formats)
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/mov', 'video/avi'];

            // Validate file type and size
            // if (!in_array($file_type, $allowed_types)) {
            //     $msg = "Invalid file type. Only images and videos are allowed.";
            // } elseif ($file_size > 5000000) { // 5MB limit
            //     $msg = "File size is too large. Maximum size is 5MB.";
            // } else {
            //     $upload_dir = 'uploads/';
            //     $upload_path = $upload_dir . basename($file_name);
            //     if (move_uploaded_file($file_tmp_name, $upload_path)) {
            //         $msg = "File uploaded successfully.";
            //     } else {
            //         $msg = "Failed to upload the file.";
            //     }
            // }
        }

        // Insert the announcement into the database
        $sql = "INSERT INTO announcements (subject, content, file_name, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $subject, $content, $file_name);

        if ($stmt->execute()) {
            $msg = "Announcement posted successfully.";
        } else {
            $msg = "Failed to post announcement.";
        }

        $stmt->close();
    } else {
        $msg = "Subject and content are required.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .alert-info {
            font-size: 1.1rem;
        }

        h2 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .additional-content {
            font-size: 1rem;
            color: #555;
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-brand, .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="HR_Dashboard.php">HR Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link" href="SurveyForm.php"><i class="fas fa-list-alt"></i> Survey Forms</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Post_Announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Workshop.php"><i class="fas fa-cogs"></i> Workshops</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="LoginAsHR.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>

        </div>
    </nav>

    <!-- Announcement Form -->
    <div class="container">
        <h2 class="mb-4">Add New Announcement</h2>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>

            <div class="mb-3">
                <label for="file" class="form-label">Attach File (Image/Video)</label>
                <input type="file" class="form-control" id="file" name="file">
            </div>

            <div class="additional-content">
                <p><strong>Note:</strong> Please ensure that the file size does not exceed 5MB. Supported formats include JPG, PNG, GIF for images and MP4, MOV, AVI for videos.</p>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Post Announcement</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
