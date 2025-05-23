<?php
// This will be the main landing page for the application.
// For now, it will be simple.
// No database connection needed for this basic index page yet.

require_once 'templates/header.php'; // Assumes header.php is in templates/ at the root
?>

<div class="jumbotron">
    <h1 class="display-4">Welcome to the IT Issue Tracker</h1>
    <p class="lead">This system helps you log, track, and manage IT issues within your organization.</p>
    <hr class="my-4">
    <p>You can log a new issue, view existing issues, or manage various entities like users, departments, categories, and devices using the navigation bar above.</p>
    <a class="btn btn-primary btn-lg" href="php/log_issue.php" role="button">Log a New Issue</a>
    <a class="btn btn-info btn-lg" href="php/view_issues.php" role="button">View Existing Issues</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Manage Users</h5>
                <p class="card-text">Add new IT assistants or view existing user accounts.</p>
                <a href="php/view_users.php" class="btn btn-secondary">Go to Users</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Manage Departments</h5>
                <p class="card-text">Define and view company departments for issue assignment.</p>
                <a href="php/view_departments.php" class="btn btn-secondary">Go to Departments</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Manage Devices</h5>
                <p class="card-text">Keep track of company devices and their serial numbers.</p>
                <a href="php/view_devices.php" class="btn btn-secondary">Go to Devices</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php'; // Assumes footer.php is in templates/ at the root
?>
