<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input from the form
    $device_type = isset($_POST['device_type']) ? trim($_POST['device_type']) : '';
    $serial_number = isset($_POST['serial_number']) ? trim($_POST['serial_number']) : '';

    // Validate input
    if (empty($device_type) || empty($serial_number)) {
        $message = "Error: Both device type and serial number are required.";
    } else {
        // Prepare an SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO devices (device_type, serial_number) VALUES (?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ss", $device_type, $serial_number);

            // Execute the statement
            if ($stmt->execute()) {
                $message = "Success: New device added successfully.";
            } else {
                // Check for specific errors, like duplicate serial number
                if ($conn->errno == 1062) { // Error number for duplicate entry
                    $message = "Error: Serial number '" . htmlspecialchars($serial_number) . "' already exists.";
                } else {
                    $message = "Error: Could not add device. " . htmlspecialchars($stmt->error);
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
        <h2>Add New Device</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_device.php" method="post">
            <div class="form-group">
                <label for="device_type">Device Type (e.g., Laptop, Desktop, Printer):</label>
                <input type="text" class="form-control" id="device_type" name="device_type" required value="<?php echo isset($_POST['device_type']) ? htmlspecialchars($_POST['device_type']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="serial_number">Serial Number:</label>
                <input type="text" class="form-control" id="serial_number" name="serial_number" required value="<?php echo isset($_POST['serial_number']) ? htmlspecialchars($_POST['serial_number']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Add Device</button>
        </form>
        <p class="mt-3"><a href="view_devices.php">View All Devices / Search Devices</a></p>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
