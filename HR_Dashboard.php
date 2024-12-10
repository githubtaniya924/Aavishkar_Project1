<?php
include 'HR_Connection.php';
session_start(); // Start a session for login tracking

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['employeeId']) && isset($_POST['password'])) {
        $employee_id = $_POST['employeeId'];
        $password = $_POST['password'];

        // Sanitize inputs
        $employee_id = $conn->real_escape_string($employee_id);
        $password = $conn->real_escape_string($password);

        if ($employee_id >= 3000 && $employee_id <= 3500) {
            $sql = "SELECT * FROM employee WHERE empno = '$employee_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {
                    $_SESSION['logged_in'] = true; // Set session variable
                    // $_SESSION['employee_name'] = $user['name']; // Optional: Store user details
                } else {
                    echo "<script>alert('Invalid Employee ID or Password.'); window.location.href = 'LoginAsHR.html';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Invalid Employee ID or Password.'); window.location.href = 'LoginAsHR.html';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Employee ID must be between 3000 and 3500.'); window.location.href = 'LoginAsHR.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Employee ID and Password are required.'); window.location.href = 'LoginAsHR.html';</script>";
        exit;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        .container {
            margin-top: 20px;
        }

        .dashboard-heading {
            margin: 20px 0;
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .icon {
            font-size: 2.5rem;
            color: #007bff;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
        }

        .btn-custom:hover {
            background-color: #007bff;
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

    <!-- Dashboard Heading -->
    <div class="container">
        <h2 class="dashboard-heading">HR Manager Dashboard</h2>

        <!-- Employee ID Form -->
        <div class="form-container mb-4">
            <h4>Check Employee Performance</h4>
            <form method="POST" action="InsertEmp_Data.php">
                <div class="mb-3">
                    <label for="employeeId" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="employeeId" name="employeeId" placeholder="Enter Employee ID" required>
                </div>
                <button type="submit" class="btn btn-custom">Check Performance</button>
            </form>
        </div>

        
<!-- HR Options -->
<div class="row">
    <!-- New Card for Adding/Editing Employee Details -->
    <div class="col-md-3">
        <div class="card text-center p-3">
            <i class="icon bi bi-person-lines-fill"></i>
            <h5 class="card-title mt-2">Employee Details</h5>
            <p class="card-text">Add new employees or edit existing employee records.</p>
            <button class="btn btn-custom" >Manage</button>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <i class="icon bi bi-upload"></i>
            <h5 class="card-title mt-2">Upload Survey Forms</h5>
            <p class="card-text">Upload employee surveys to evaluate performance.</p>
            <button class="btn btn-custom"><a href="SurveyForm.php" style="color:white;text-decoration:none;">Upload</a></button>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <i class="icon bi bi-megaphone"></i>
            <h5 class="card-title mt-2">Post Announcements</h5>
            <p class="card-text">Share updates or new policies with employees.</p>
            <button class="btn btn-custom"><a href="Post_Announcement.php" style="color:white;text-decoration:none;">Post</a></button>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <i class="icon bi bi-calendar-event"></i>
            <h5 class="card-title mt-2">Workshops & Training</h5>
            <p class="card-text">Manage and update training programs.</p>
            <button class="btn btn-custom"><a href="Workshop.php" style="color:white;text-decoration:none;">Update</a></button>
        </div>
    </div>
</div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function uploadSurvey() {
            alert("Redirecting to the survey upload page...");
            window.location.href = "upload_survey.php"; // Example redirection
        }

        function postAnnouncement() {
            alert("Redirecting to the announcements page...");
            window.location.href = "post_announcement.php"; // Example redirection
        }

        function updateWorkshops() {
            alert("Redirecting to the workshops management page...");
            window.location.href = "workshop_update.php"; // Example redirection
        }
    </script>
</body>
</html>
