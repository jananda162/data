<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$categories = [];
$error_message = '';

// Prepare and execute SQL statement to fetch issue categories
$sql = "SELECT id, name FROM issue_categories ORDER BY name ASC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Fetch all categories
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    // No specific message if no categories, the HTML will handle it.
} else {
    $error_message = "Error fetching issue categories: " . htmlspecialchars($conn->error);
}

// Close the connection
$conn->close();

require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Issue Categories List</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($categories) && empty($error_message)): ?>
            <div class="alert alert-info" role="alert">
                No issue categories found. You can add one using the link below.
            </div>
        <?php elseif (!empty($categories)): ?>
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_issue_category.php" class="btn btn-primary">Add New Issue Category</a>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>
