<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$is_ajax_request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$message = ''; // For HTML response
$response = []; // For JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = isset($_POST['department_name']) ? trim($_POST['department_name']) : '';

    if (empty($department_name)) {
        if ($is_ajax_request) {
            $response = ['success' => false, 'message' => 'Department name is required.'];
        } else {
            $message = "Error: Department name is required.";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        if ($stmt) {
            $stmt->bind_param("s", $department_name);
            if ($stmt->execute()) {
                $last_id = $conn->insert_id;
                if ($is_ajax_request) {
                    $response = ['success' => true, 'id' => $last_id, 'name' => $department_name, 'message' => 'Department added successfully.'];
                } else {
                    $message = "Success: New department added successfully.";
                }
            } else {
                $error_detail = htmlspecialchars($stmt->error);
                if ($conn->errno == 1062) {
                    $error_detail = "Department name '" . htmlspecialchars($department_name) . "' already exists.";
                }
                if ($is_ajax_request) {
                    $response = ['success' => false, 'message' => $error_detail];
                } else {
                    $message = "Error: " . $error_detail;
                }
            }
            $stmt->close();
        } else {
            $error_detail = htmlspecialchars($conn->error);
            if ($is_ajax_request) {
                $response = ['success' => false, 'message' => "Could not prepare the SQL statement. " . $error_detail];
            } else {
                $message = "Error: Could not prepare the SQL statement. " . $error_detail;
            }
        }
    }
    $conn->close();

    if ($is_ajax_request) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// If not an AJAX request or not a POST request, display the HTML form.
// (The $conn would be closed if POST was processed, so re-open for GET if needed, or ensure db_connect can handle it)
// For simplicity, we assume for non-AJAX, $conn is still open if it's a GET request, or closed if it was a non-AJAX POST.
// If it's a GET request, we need $conn for the header/footer if they use it.
if ($_SERVER["REQUEST_METHOD"] != "POST") { // If it's a GET request, ensure connection is open for templates
    // The db_connect.php is included at the top, $conn should be available if not POST.
    // If it was a non-AJAX POST, $conn is closed. This is fine as we won't re-render the form with old values.
}


require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Add New Department</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($message)): // This message is for non-AJAX POST requests ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_department.php" method="post" id="addDepartmentForm_page">
            <div class="form-group">
                <label for="department_name_page">Department Name:</label>
                <input type="text" class="form-control" id="department_name_page" name="department_name" required value="">
            </div>
            <button type="submit" class="btn btn-primary">Add Department</button>
        </form>
        <p class="mt-3"><a href="view_departments.php">View All Departments</a></p>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
