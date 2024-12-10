<?php
// Database credentials
$host = "localhost";
$dbname = "EmployeeData";
$username = "root";
$password = "";

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeId = $_POST['employeeId'];
    $performancePercentage = $_POST['performancePercentage'];
    $tasksCompleted = $_POST['tasksCompleted'];
    $tasksPending = $_POST['tasksPending'];
    $credits = $_POST['credits'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Check if employee exists
    $checkQuery = "SELECT * FROM employees WHERE empno = $employeeId'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Insert data into the performance table
        $insertQuery = "INSERT INTO performance (performance_percentage, tasks_completed, tasks_pending, credits, month, year) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("siiissi", $performancePercentage, $tasksCompleted, $tasksPending, $credits, $month, $year);

        if ($insertStmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Data inserted successfully for Employee ID: $employeeId.",
                "redirect" => "HR_Dashboard.php"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to insert data."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Employee not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
?>
