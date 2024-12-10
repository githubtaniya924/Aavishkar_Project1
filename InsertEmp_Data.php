<?php
include 'HR_Connection.php';
session_start(); // Start a session for login tracking

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_performance'])) {
    // Get form data
    $employee_id = $_POST['employeeId'];
    $performance_percentage = $_POST['performance_percentage'];
    $completed_tasks = $_POST['completed_tasks'];
    $pending_tasks = $_POST['pending_tasks'];
    $credits = $_POST['credits'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Sanitize inputs
    $employee_id = $conn->real_escape_string($employee_id);
    $performance_percentage = $conn->real_escape_string($performance_percentage);
    $completed_tasks = $conn->real_escape_string($completed_tasks);
    $pending_tasks = $conn->real_escape_string($pending_tasks);
    $credits = $conn->real_escape_string($credits);
    $month = $conn->real_escape_string($month);
    $year = $conn->real_escape_string($year);

    // Insert into the performance table
    $sql = "INSERT INTO performance (empno, performance_percentage, completed_tasks, pending_tasks, credits, month, year)
            VALUES ('$employee_id', '$performance_percentage', '$completed_tasks', '$pending_tasks', '$credits', '$month', '$year')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Performance Data Added Successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
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
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar { background-color: #343a40; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { text-decoration: underline; }
        .container { margin-top: 20px; }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="HR_Dashboard.php">HR Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="SurveyForm.php">Survey Forms</a></li>
                <li class="nav-item"><a class="nav-link" href="Post_Announcement.php">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="Workshop.php">Workshops</a></li>
                <li class="nav-item"><a class="nav-link" href="LoginAsHR.html">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Heading -->
<div class="container">
    <h2 class="dashboard-heading text-center">HR Manager Dashboard</h2>

    <!-- Performance Form -->
    <div class="form-container">
        <h4>Enter Employee Performance Details</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="employeeId" class="form-label">Employee ID</label>
                <input type="text" class="form-control" id="employeeId" name="employeeId" required>
            </div>
            <div class="mb-3">
                <label for="performance_percentage" class="form-label">Performance Percentage</label>
                <input type="number" class="form-control" id="performance_percentage" name="performance_percentage" required>
            </div>
            <div class="mb-3">
                <label for="completed_tasks" class="form-label">Completed Tasks</label>
                <input type="number" class="form-control" id="completed_tasks" name="completed_tasks" required>
            </div>
            <div class="mb-3">
                <label for="pending_tasks" class="form-label">Pending Tasks</label>
                <input type="number" class="form-control" id="pending_tasks" name="pending_tasks" required>
            </div>
            <div class="mb-3">
                <label for="credits" class="form-label">Credits</label>
                <input type="number" class="form-control" id="credits" name="credits" required>
            </div>
            <div class="mb-3">
                <label for="month" class="form-label">Month</label>
                <input type="text" class="form-control" id="month" name="month" required>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" name="year" required>
            </div>
            <button type="submit" name="submit_performance" class="btn btn-custom">Submit</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
