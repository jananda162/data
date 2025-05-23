<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$issues = [];
$page_error_message = ''; // For errors like DB connection issues if not handled by db_connect

// SQL query to fetch issues with all related information
$sql = "
    SELECT 
        i.id AS issue_id,
        i.description AS issue_description,
        i.created_at AS issue_created_at,
        i.resolved_at AS issue_resolved_at,
        u.name AS user_name,
        u.username AS user_username,
        d.name AS department_name,
        ic.name AS category_name,
        dev.device_type AS device_type,
        dev.serial_number AS device_serial_number
    FROM issues i
    JOIN users u ON i.user_id = u.id
    JOIN departments d ON i.department_id = d.id
    JOIN issue_categories ic ON i.category_id = ic.id
    LEFT JOIN devices dev ON i.device_id = dev.id
    ORDER BY i.created_at DESC, i.id DESC
";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $issues[] = $row;
        }
    }
    // If num_rows is 0, $issues array will remain empty, handled in HTML.
} else {
    // Error in query execution
    $page_error_message = "Error fetching issues: " . htmlspecialchars($conn->error);
}

$conn->close();

// Include header template
require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Logged IT Issues</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($page_error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $page_error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($issues) && empty($page_error_message)): ?>
            <div class="alert alert-info" role="alert">
                No IT issues logged yet. <a href="log_issue.php" class="alert-link">Log a new issue</a>.
            </div>
        <?php elseif (!empty($issues)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Reported By</th>
                            <th>Department</th>
                            <th>Category</th>
                            <th>Device</th>
                            <th>Description</th>
                            <th>Reported At</th>
                            <th>Resolved At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($issue['issue_id']); ?></td>
                                <td><?php echo htmlspecialchars($issue['user_name'] ?: $issue['user_username']); ?></td>
                                <td><?php echo htmlspecialchars($issue['department_name']); ?></td>
                                <td><?php echo htmlspecialchars($issue['category_name']); ?></td>
                                <td>
                                    <?php 
                                    if (!empty($issue['device_type']) && !empty($issue['device_serial_number'])) {
                                        echo htmlspecialchars($issue['device_type'] . ' - ' . $issue['device_serial_number']);
                                    } else {
                                        echo '<span class="text-muted">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($issue['issue_description']); ?>">
                                    <?php echo htmlspecialchars($issue['issue_description']); ?>
                                </td>
                                <td><?php echo date("Y-m-d H:i:s", strtotime($issue['issue_created_at'])); ?></td>
                                <td>
                                    <?php 
                                    if ($issue['issue_resolved_at']) {
                                        echo date("Y-m-d H:i:s", strtotime($issue['issue_resolved_at']));
                                    } else {
                                        echo '<span class="badge badge-warning">Not Resolved</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (!$issue['issue_resolved_at']): ?>
                                        <button class="btn btn-sm btn-success disabled" title="Functionality to be added">Mark Resolved</button>
                                    <?php else: ?>
                                         <span class="badge badge-success">Resolved</span>
                                    <?php endif; ?>
                                    <!-- Add other actions like 'View Details' later if needed -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if (empty($page_error_message)): // Show "Log New Issue" button if page loaded correctly ?>
        <hr>
        <a href="log_issue.php" class="btn btn-primary">Log New Issue</a>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer template
require_once '../templates/footer.php';
?>
