<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$is_ajax_request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$message = ''; // For HTML response
$response = []; // For JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';

    if (empty($category_name)) {
        if ($is_ajax_request) {
            $response = ['success' => false, 'message' => 'Category name is required.'];
        } else {
            $message = "Error: Category name is required.";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO issue_categories (name) VALUES (?)");
        if ($stmt) {
            $stmt->bind_param("s", $category_name);
            if ($stmt->execute()) {
                $last_id = $conn->insert_id;
                if ($is_ajax_request) {
                    $response = ['success' => true, 'id' => $last_id, 'name' => $category_name, 'message' => 'Category added successfully.'];
                } else {
                    $message = "Success: New issue category added successfully.";
                }
            } else {
                $error_detail = htmlspecialchars($stmt->error);
                if ($conn->errno == 1062) {
                    $error_detail = "Category name '" . htmlspecialchars($category_name) . "' already exists.";
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
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Connection is still open from the top include for GET requests
}

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Add New Issue Category</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($message)): // This message is for non-AJAX POST requests ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="add_issue_category.php" method="post" id="addCategoryForm_page">
            <div class="form-group">
                <label for="category_name_page">Category Name:</label>
                <input type="text" class="form-control" id="category_name_page" name="category_name" required value="">
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
        <p class="mt-3"><a href="view_issue_categories.php">View All Issue Categories</a></p>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
