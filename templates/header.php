<?php
// Determine base path for links
// Check if SCRIPT_FILENAME is set and not empty
if (!empty($_SERVER['SCRIPT_FILENAME'])) {
    // Check if DOCUMENT_ROOT is set and not empty, and if SCRIPT_FILENAME is under DOCUMENT_ROOT
    $doc_root_set = !empty($_SERVER['DOCUMENT_ROOT']) && strpos(realpath($_SERVER['SCRIPT_FILENAME']), realpath($_SERVER['DOCUMENT_ROOT'])) === 0;
    
    if ($doc_root_set) {
        $is_root_index = (basename($_SERVER['SCRIPT_FILENAME']) === 'index.php' && realpath(dirname($_SERVER['SCRIPT_FILENAME'])) === realpath($_SERVER['DOCUMENT_ROOT']));
    } else {
        // Fallback if DOCUMENT_ROOT is not set or not matching: check relative path from a known structure
        // This assumes 'index.php' is at root and other scripts are in 'php/' relative to 'templates/'
        // This fallback is less robust and might need adjustment based on actual server config
        $is_root_index = (basename($_SERVER['SCRIPT_FILENAME']) === 'index.php' && file_exists('templates/header.php')); 
    }
} else {
    // Fallback if SCRIPT_FILENAME is not available (e.g. some CLI execution contexts)
    // Defaulting to paths suitable for scripts in 'php/' directory for safety in typical use case.
    $is_root_index = false; 
}

$base_app_path = $is_root_index ? '' : '../'; 
$base_php_path = $is_root_index ? 'php/' : ''; 
?>
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
        <a class="navbar-brand" href="<?php echo $base_app_path; ?>index.php">IT Issue Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_app_path; ?>index.php">Log New Issue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_php_path; ?>view_issues.php">View Issues</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownManage" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownManage">
                        <a class="dropdown-item" href="<?php echo $base_php_path; ?>view_users.php">Users</a>
                        <a class="dropdown-item" href="<?php echo $base_php_path; ?>view_departments.php">Departments</a>
                        <a class="dropdown-item" href="<?php echo $base_php_path; ?>view_issue_categories.php">Categories</a>
                        <a class="dropdown-item" href="<?php echo $base_php_path; ?>view_devices.php">Devices</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                        <a class="dropdown-item" href="<?php echo $base_php_path; ?>report_frequent_issues.php">Frequent Issues</a>
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
