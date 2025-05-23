<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Issue Tracker</title>
    <!-- Bootstrap CSS from CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        /* Custom styles can go here if needed */
        .navbar { margin-bottom: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="../index.php">IT Issue Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="log_issue.php">Log New Issue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_issues.php">View Issues</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="manageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </a>
                    <div class="dropdown-menu" aria-labelledby="manageDropdown">
                        <a class="dropdown-item" href="view_users.php">Users</a>
                        <a class="dropdown-item" href="view_departments.php">Departments</a>
                        <a class="dropdown-item" href="view_issue_categories.php">Categories</a>
                        <a class="dropdown-item" href="view_devices.php">Devices</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <a class="dropdown-item" href="report_frequent_issues.php">Frequent Issues</a>
                        <!-- Add more reports here later if needed -->
                    </div>
                </li>
            </ul>
            <!-- Optional: Add items to the right like a search form or user login status -->
            <!-- <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Login</a>
                </li>
            </ul> -->
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page-specific content will go here -->
