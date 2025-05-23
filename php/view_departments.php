<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$departments = [];
$error_message = '';

// Prepare and execute SQL statement to fetch departments
$sql = "SELECT id, name FROM departments ORDER BY name ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Fetch all departments
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    }
    // No specific message if no departments, the HTML will handle it.
} else {
    $error_message = "Error fetching departments: " . htmlspecialchars($conn->error);
}

// Close the connection
$conn->close();

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Departments List</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($departments) && empty($error_message)): ?>
            <div class="alert alert-info" role="alert">
                No departments found. You can add one using the link below.
            </div>
        <?php elseif (!empty($departments)): ?>
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Department Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($department['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_department.php" class="btn btn-primary">Add New Department</a>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
