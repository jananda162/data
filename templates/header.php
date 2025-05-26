<?php
// Get the server's document root (e.g., /var/www/html or C:/Apache24/htdocs)
// Ensure consistent directory separators for comparison
$document_root = str_replace('\', '/', $_SERVER['DOCUMENT_ROOT']); // Corrected backslash

// Determine the base path of the application from SCRIPT_NAME
// SCRIPT_NAME for http://localhost:85/data/index.php is /data/index.php
// SCRIPT_NAME for http://localhost:85/data/php/view_issues.php is /data/php/view_issues.php
$script_dir = dirname($_SERVER['SCRIPT_NAME']);

if (basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
    // If index.php is the current script, its directory is the project root path
    $base_project_path_from_doc_root = $script_dir;
} else {
    // If the current script is not index.php, it's likely in a subdirectory (like 'php/')
    // Assume 'php/' is directly under the project root where 'index.php' resides.
    // So, go one level up from the script's directory.
    $base_project_path_from_doc_root = dirname($script_dir);
}

// Clean up potential leading/trailing slashes and ensure a single leading slash if not empty,
// and a single trailing slash.
$app_base_url = rtrim($base_project_path_from_doc_root, '/\\'); // Note: rtrim might not need '\\' if $script_dir uses only '/'
if ($app_base_url === '' || $app_base_url === '.' || $app_base_url === '/.') {
    // Project is at the document root or path resolves to something like '.'
    $app_base_url = '/';
} else {
    // Ensure leading slash if it's a subdirectory
    if (strpos($app_base_url, '/') !== 0) {
        $app_base_url = '/' . $app_base_url;
    }
    $app_base_url .= '/'; // Ensure trailing slash
}

// For links that go to the application root (index.php)
$link_to_app_root = $app_base_url . 'index.php';

// For links that go to files within the 'php' subdirectory
$link_to_php_files_base = $app_base_url . 'php/';

/*
// Temporary debug (optional, remove after testing):
echo "<!--\n";
echo "DOCUMENT_ROOT: " . $document_root . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "dirname(SCRIPT_NAME): " . dirname($_SERVER['SCRIPT_NAME']) . "\n";
echo "Calculated app_base_url: " . $app_base_url . "\n";
echo "Link to app root: " . $link_to_app_root . "\n";
echo "Link to php files base: " . $link_to_php_files_base . "\n";
echo "-->\n";
*/
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

        /* Custom primary color override */
       .bg-primary {
           background-color: #0056b3 !important;
       }
       .btn-primary {
           background-color: #0056b3 !important;
           border-color: #0056b3 !important;
       }
       .btn-primary:hover {
           background-color: #004085 !important; /* A darker shade for hover */
           border-color: #004085 !important;
       }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="<?php echo $link_to_app_root; ?>">IT Issue Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_to_app_root; ?>">Log New Issue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_to_php_files_base; ?>view_issues.php">View Issues</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownManage" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownManage">
                        <a class="dropdown-item" href="<?php echo $link_to_php_files_base; ?>view_users.php">Users</a>
                        <a class="dropdown-item" href="<?php echo $link_to_php_files_base; ?>view_departments.php">Departments</a>
                        <a class="dropdown-item" href="<?php echo $link_to_php_files_base; ?>view_issue_categories.php">Categories</a>
                        <a class="dropdown-item" href="<?php echo $link_to_php_files_base; ?>view_devices.php">Devices</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                        <a class="dropdown-item" href="<?php echo $link_to_php_files_base; ?>report_frequent_issues.php">Frequent Issues</a>
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
