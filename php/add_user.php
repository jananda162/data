<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input from the form
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input
    if (empty($username) || empty($name) || empty($password)) {
        $message = "Error: All fields are required.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, name, password) VALUES (?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $name, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                $message = "Success: New user added successfully.";
            } else {
                // Check for specific errors, like duplicate username
                if ($conn->errno == 1062) { // Error number for duplicate entry
                    $message = "Error: Username '" . htmlspecialchars($username) . "' already exists.";
                } else {
                    $message = "Error: Could not add user. " . htmlspecialchars($stmt->error);
                }
            }
            // Close the statement
            $stmt->close();
        } else {
            $message = "Error: Could not prepare the SQL statement. " . htmlspecialchars($conn->error);
        }
    }
    // Close the connection
    $conn->close();
}

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Add New IT Assistant</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_user.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
        <p class="mt-3"><a href="view_users.php">View All Users</a></p>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
