<?php
include 'Emp_Connection.php';
session_start(); // Start a session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['employeeId']) && isset($_POST['password'])) {
        $employee_id = $_POST['employeeId'];
        $password = $_POST['password'];

        // Sanitize inputs
        $employee_id = $conn->real_escape_string($employee_id);
        $password = $conn->real_escape_string($password);

        if ($employee_id >= 1000 && $employee_id <= 3500) {
            $sql = "SELECT * FROM employee WHERE empno = '$employee_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['employee_id'] = $user['empno'];
                    $_SESSION['employee_name'] = $user['name'];
                    $_SESSION['role'] = $user['desig'];
                    $_SESSION['department'] = $user['dept'];
                } else {
                    echo "<script>alert('Invalid Employee ID or Password.'); window.location.href = 'LoginAsEmp.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Invalid Employee ID or Password.'); window.location.href = 'LoginAsEmp.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Employee ID must be between 1000 and 3500.'); window.location.href = 'LoginAsEmp.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Employee ID and Password are required.'); window.location.href = 'LoginAsEmp.php';</script>";
        exit;
    }

// Fetch performance data
$sql = "SELECT completed_tasks, pending_tasks, performance_percentage FROM performance WHERE empno = '{$_SESSION['employee_id']}'";
$result = $conn->query($sql);

$tasksCompleted = 0;
$tasksPending = 0;
$performance_percentage = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tasksCompleted = $row['completed_tasks'];
    $tasksPending = $row['pending_tasks'];
    $performance_percentage = $row['performance_percentage'];
}

}

$conn->close();
?>

<script>
    // Pass PHP variables to JavaScript
    const tasksCompleted = <?php echo $tasksCompleted; ?>;
    const tasksPending = <?php echo $tasksPending; ?>;
    const performance_percentage = <?php echo $performance_percentage; ?>;


</script>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Performance Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: var(--navbar-bg, #1a202c);
            padding: 15px 20px;
        }

        .navbar-brand,
        .nav-link {
            color: var(--navbar-text, #f8f9fa) !important;
            font-weight: 500;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        /* Header Section */
        .dashboard-header {
            text-align: center;
            margin: 30px 0;
            color: var(--text-color, #212529);
        }

        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: bold;
        }

        .dashboard-header p {
            color: var(--subtext-color, #495057);
        }

        /* Employee Details */
        .employee-card {
            background: var(--card-bg, #007bff);
            color: var(--card-text, white);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Cards Section */
        .card {
            border-radius: 15px;
            padding: 20px;
            background: var(--card-bg, white);
            color: var(--text-color, #212529);
            border: 1px solid #dee2e6;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--card-header-color, #1a202c);
        }

        /* Chart Styling */
        canvas {
            max-height: 180px;
        }

        /* Footer */
        footer {
            background-color: var(--navbar-bg, #1a202c);
            color: var(--footer-text, white);
            padding: 10px 0;
            text-align: center;
        }
        body {
    background-color: var(--background-color);
    color: var(--text-color); /* Match text color with the theme */
    transition: background-color 0.5s ease, color 0.5s ease; /* Smooth transition */
}
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-chart-line"></i> Performance Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="Emp_Dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="Reports.html"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                            <li><button class="dropdown-item theme-option" data-theme="light">Light</button></li>
                            <li><button class="dropdown-item theme-option" data-theme="dark">Dark</button></li>
                            <li><button class="dropdown-item theme-option" data-theme="pastel">Pastel</button></li>
                            <li><button class="dropdown-item theme-option" data-theme="high-contrast">High Contrast</button></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="About_page1.html"><i class="fa-solid fa-address-card"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="LoginAsEmp.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-user-circle"></i> Welcome, <?php echo $_SESSION['employee_name']; ?>!</h1>
        <p>Your performance overview</p>
    </div>

    <!-- Employee Details -->
    <div class="container">
        <div class="employee-card">
            <h4>Employee Details</h4>
            <p><strong>ID:</strong> <?php echo $_SESSION['employee_id']; ?> | 
           <strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
        <p><strong>Department:</strong> <?php echo $_SESSION['department']; ?> | 
           <strong>Name:</strong> <?php echo $_SESSION['employee_name']; ?></p>
        </div>

        <div class="row">
        <form id="performanceForm" method="POST">
    <input type="hidden" name="month" id="monthInput">
    <input type="hidden" name="year" id="yearInput">

    <div class="row justify-content-center">
        <div class="col-md-4">
            <label for="monthSelect">Select Month</label>
            <select id="monthSelect" class="form-control" onchange="updateCharts()">
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="2">November</option>
                <!-- Other months... -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="yearSelect">Select Year</label>
            <select id="yearSelect" class="form-control" onchange="updateCharts()">
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
        </div>
    </div>
</form>

        </div>

        <!-- Performance Metrics -->
        <div class="row">
            <!-- Pie Chart -->
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-header"><i class="fas fa-chart-pie"></i> Overall Performance</div>
                    <canvas id="performancePieChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-header"><i class="fas fa-tasks"></i> Task Completion</div>
                    <canvas id="taskBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-header"><i class="fas fa-clock"></i> Recent Activities</div>
                    <ul>
                        <li>Completed Project A successfully</li>
                        <li>Attended a training session</li>
                        <li>Achieved Q4 target</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-header"><i class="fas fa-calendar-alt"></i> Upcoming Deadlines</div>
                    <ul>
                        <li>Client meeting: Dec 12</li>
                        <li>Submit report: Dec 15</li>
                        <li>Team training: Dec 20</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Employee Performance Tracker. All Rights Reserved.
    </footer>

    <!-- Charts -->
    <script>
        // Pie Chart for Overall Performance
        // Pie Chart for Overall Performance
const performancePieCtx = document.getElementById('performancePieChart').getContext('2d');
new Chart(performancePieCtx, {
    type: 'pie',
    data: {
        labels: ['Achieved', 'Remaining'],
        datasets: [{
            data: [performance_percentage, 100 - performance_percentage],
            backgroundColor: ['#007bff', '#dee2e6']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Bar Chart for Task Completion
const taskBarCtx = document.getElementById('taskBarChart').getContext('2d');
new Chart(taskBarCtx, {
    type: 'bar',
    data: {
        labels: ['Completed', 'Pending'],
        datasets: [{
            label: 'Tasks',
            data: [tasksCompleted, tasksPending],
            backgroundColor: ['#007bff', '#dee2e6']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Theme Changing Script
const themes = {
            light: {
        '--background-color': '#f8f9fa',
        '--navbar-bg': '#1a202c',
        '--navbar-text': '#f8f9fa',
        '--text-color': '#212529',
        '--subtext-color': '#495057',
        '--card-bg': '#ffffff',
        '--card-text': '#212529',
        '--footer-text': '#212529',
        '--card-header-color': '#1a202c',
        '--chart-bg': '#ffffff', /* New */
        '--chart-text': '#212529' /* New */
    },
    
            dark: {
        '--background-color': '#121212',
        '--navbar-bg': '#212529',
        '--navbar-text': '#f8f9fa',
        '--text-color': '#f8f9fa',
        '--subtext-color': '#dee2e6',
        '--card-bg': '#343a40',
        '--card-text': '#f8f9fa',
        '--footer-text': '#dee2e6',
        '--card-header-color': '#f8f9fa',
        '--chart-bg': '#343a40', /* New */
        '--chart-text': '#f8f9fa' /* New */
    },
            pastel: {
                '--background-color': '#f8f9fa',
                '--navbar-bg': '#f8d7da',
                '--navbar-text': '#721c24',
                '--text-color': '#495057',
                '--subtext-color': '#6c757d',
                '--card-bg': '#fff3cd',
                '--card-text': '#495057',
                '--footer-text': '#721c24',
                '--card-header-color': '#856404'
            },
            'high-contrast': {
                '--navbar-bg': '#000000',
                '--navbar-text': '#ffffff',
                '--text-color': '#ffffff',
                '--subtext-color': '#ffffff',
                '--card-bg': '#000000',
                '--card-text': '#ffffff',
                '--footer-text': '#ffffff',
                '--card-header-color': '#ffffff'
            }
        };

        document.querySelectorAll('.theme-option').forEach(button => {
            button.addEventListener('click', () => {
                const theme = button.getAttribute('data-theme');
                const styles = themes[theme];
                Object.keys(styles).forEach(property => {
                    document.documentElement.style.setProperty(property, styles[property]);
                });
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
