<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$users = [];
$error_message = '';

// Prepare and execute SQL statement to fetch users
$sql = "SELECT id, username, name FROM users ORDER BY name ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Fetch all users
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    // No specific message if no users, the HTML will handle it.
} else {
    $error_message = "Error fetching users: " . htmlspecialchars($conn->error);
}

// Close the connection
$conn->close();

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>IT Assistants List</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($users) && empty($error_message)): ?>
            <div class="alert alert-info" role="alert">
                No IT assistants found. You can add one using the link below.
            </div>
        <?php elseif (!empty($users)): ?>
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_user.php" class="btn btn-primary">Add New User</a>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
