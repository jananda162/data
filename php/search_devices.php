<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$is_ajax_request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$response = []; // For JSON response

$search_results_html = []; // For HTML response (renamed from $search_results to avoid confusion)
$error_message_html = ''; // For HTML response
$search_term_html = '';   // For HTML response

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search_term'])) {
    $search_term = trim($_GET['search_term']);
    $search_term_html = $search_term; // Keep for HTML form repopulation

    if (empty($search_term)) {
        if ($is_ajax_request) {
            $response = ['success' => false, 'message' => 'Search term is required.'];
        } else {
            $error_message_html = "Please enter a serial number to search.";
        }
    } else {
        $stmt = $conn->prepare("SELECT id, device_type, serial_number FROM devices WHERE serial_number LIKE ? ORDER BY serial_number ASC");
        if ($stmt) {
            $like_search_term = "%" . $search_term . "%";
            $stmt->bind_param("s", $like_search_term);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $devices_data = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $devices_data[] = $row;
                    }
                }
                if ($is_ajax_request) {
                    $response = ['success' => true, 'devices' => $devices_data];
                } else {
                    $search_results_html = $devices_data; // For HTML display
                }
            } else {
                $error_detail = htmlspecialchars($stmt->error);
                if ($is_ajax_request) {
                    $response = ['success' => false, 'message' => "Error executing search: " . $error_detail];
                } else {
                    $error_message_html = "Error executing search: " . $error_detail;
                }
            }
            $stmt->close();
        } else {
            $error_detail = htmlspecialchars($conn->error);
            if ($is_ajax_request) {
                $response = ['success' => false, 'message' => "Error preparing search statement: " . $error_detail];
            } else {
                $error_message_html = "Error preparing search statement: " . $error_detail;
            }
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && !isset($_GET['search_term'])) {
    // This case is primarily for non-AJAX direct access without search_term
    if (!$is_ajax_request) {
        $error_message_html = "Please use the search form on the 'View Devices' page or enter a search term.";
    } else {
        // For AJAX, if search_term is not set, it's an invalid request.
        // This should ideally be caught by client-side validation before sending.
        $response = ['success' => false, 'message' => 'Search term parameter is missing.'];
    }
}

$conn->close();

if ($is_ajax_request) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- HTML Page Rendering (Non-AJAX) ---
require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Device Search Results</h2>
    </div>
    <div class="card-body">
        <form action="search_devices.php" method="get" class="form-inline mb-3">
            <div class="form-group mr-2">
                <input type="text" name="search_term" class="form-control" placeholder="Enter serial number" value="<?php echo htmlspecialchars($search_term_html); ?>">
            </div>
            <button type="submit" class="btn btn-info">Search Again</button>
        </form>

        <?php if (!empty($error_message_html)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message_html; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['search_term']) && !empty($search_term_html) && empty($search_results_html) && empty($error_message_html)): ?>
            <div class="alert alert-warning" role="alert">
                No devices found matching serial number '<?php echo htmlspecialchars($search_term_html); ?>'.
            </div>
        <?php elseif (!empty($search_results_html)): ?>
            <h3 class="mt-4">Results for "<?php echo htmlspecialchars($search_term_html); ?>":</h3>
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Device Type</th>
                        <th>Serial Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results_html as $device): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($device['device_type']); ?></td>
                            <td><?php echo htmlspecialchars($device['serial_number']); ?></td>
                            <td>
                                <a href="log_issue.php?device_id=<?php echo $device['id']; ?>&device_info=<?php echo urlencode(htmlspecialchars($device['device_type']) . ' - ' . htmlspecialchars($device['serial_number'])); ?>" class="btn btn-sm btn-success">Select for Issue</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="mt-3">
            <a href="view_devices.php" class="btn btn-secondary">View All Devices</a>
            <a href="add_device.php" class="btn btn-info">Add New Device</a>
        </div>
        <!-- The "Back to Home" link is now in the main navigation bar from header.php -->
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
