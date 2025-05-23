<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$devices = [];
$error_message = '';

// Prepare and execute SQL statement to fetch devices
$sql = "SELECT id, device_type, serial_number FROM devices ORDER BY device_type ASC, serial_number ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Fetch all devices
        while ($row = $result->fetch_assoc()) {
            $devices[] = $row;
        }
    }
    // No specific message if no devices, the HTML will handle it.
} else {
    $error_message = "Error fetching devices: " . htmlspecialchars($conn->error);
}

// Close the connection (will be closed by db_connect.php if not already, but good practice if more logic followed)
// $conn->close(); // Not strictly needed here as script ends.

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Device List</h2>
    </div>
    <div class="card-body">
        <form action="search_devices.php" method="get" class="form-inline mb-3">
            <div class="form-group mr-2">
                <input type="text" name="search_term" class="form-control" placeholder="Enter serial number" value="<?php echo isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-success">Search Devices</button>
        </form>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($devices) && empty($error_message)): ?>
            <div class="alert alert-info" role="alert">
                No devices found. You can add one using the link below.
            </div>
        <?php elseif (!empty($devices)): ?>
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Device Type</th>
                        <th>Serial Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($device['device_type']); ?></td>
                            <td><?php echo htmlspecialchars($device['serial_number']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_device.php" class="btn btn-primary">Add New Device</a>
        <!-- The "Back to Home" link is now in the main navigation bar from header.php -->
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
