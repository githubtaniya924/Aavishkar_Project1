<?php
// Database connection
$host = 'localhost:3307';
$username = 'root';
$password = '';
$dbname = 'EmployeeData';

// Create a connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables
$department = isset($_GET['department']) ? $_GET['department'] : 'All';
$surveyLink = isset($_GET['surveyLink']) ? $_GET['surveyLink'] : '';
$msg = "";

// Handle sending survey link to filtered employees
if (isset($_GET['action']) && $_GET['action'] === 'sendToFiltered' && !empty($surveyLink)) {
    if ($department !== 'All') {
        $stmt = $mysqli->prepare("UPDATE employee SET surveyLink = ? WHERE dept = ?");
        $stmt->bind_param("ss", $surveyLink, $department);
        if ($stmt->execute()) {
            header("Location: ?department=" . urlencode($department) . "&msg=Survey+link+sent+to+filtered+employees");
            exit;
        } else {
            $msg = "Failed to send survey link: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        $stmt = $mysqli->prepare("UPDATE employee SET surveyLink = ?");
        $stmt->bind_param("s", $surveyLink);
        if ($stmt->execute()) {
            header("Location: ?department=All&msg=Survey+link+sent+to+all+employees");
            exit;
        } else {
            $msg = "Failed to send survey link: " . $mysqli->error;
        }
        $stmt->close();
    }
}

// Fetch employees based on department for display
if ($department !== 'All') {
    $stmt = $mysqli->prepare("SELECT empno, dept, surveyLink FROM employee WHERE dept = ?");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query("SELECT empno, dept, surveyLink FROM employee");
}

// Fetch employees into an array
$employees = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Close the connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Link Distribution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h1 class="text-center my-4">HR Survey Link Distribution</h1>

    <div class="container">
        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="get" action="">
        <button type="reset" class="btn btn-primary" style="float:right;margin-bottom:4px">Refresh</button>
            <label for="surveyLink">Enter Google Form Survey Link:</label>
            <input type="text" id="surveyLink" name="surveyLink" value="<?php echo htmlspecialchars($surveyLink); ?>" placeholder="Enter your survey link" class="form-control mb-3">
            <label for="department">Select Department:</label>
            <select id="department" name="department" class="form-select mb-3">
                <option value="All" <?php echo ($department === 'All') ? 'selected' : ''; ?>>All</option>
                <option value="HR" <?php echo ($department === 'HR') ? 'selected' : ''; ?>>HR</option>
                <option value="IT" <?php echo ($department === 'IT') ? 'selected' : ''; ?>>IT</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>

            <?php if (!empty($employees)): ?>
                <button type="button" class="btn btn-success" onclick="sendToFiltered()">Send Link to Filtered Employees</button>
            <?php endif; ?>
        </form>

        <h2>Employee List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Survey Link</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr><td colspan="3">No employees found.</td></tr>
                <?php else: ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['empno']); ?></td>
                            <td><?php echo htmlspecialchars($employee['dept']); ?></td>
                            <td><?php echo htmlspecialchars($employee['surveyLink']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function sendToFiltered() {
            const surveyLink = document.getElementById('surveyLink').value;
            const department = document.getElementById('department').value;
            if (!surveyLink) {
                alert("Please enter a survey link before sending.");
                return;
            }
            window.location.href = `?action=sendToFiltered&surveyLink=${encodeURIComponent(surveyLink)}&department=${encodeURIComponent(department)}`;
        }

        // Display success message from query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');
        if (msg) {
            alert(decodeURIComponent(msg)); // Decode the message to display special characters correctly
        }
    </script>
</body>
</html>
