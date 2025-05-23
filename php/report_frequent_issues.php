<?php
// Include the database connection script
require_once 'includes/db_connect.php';

$frequent_issues = [];
$page_error_message = '';

// SQL query to fetch frequent issue categories
$sql = "
    SELECT 
        ic.name AS category_name,
        COUNT(i.id) AS issue_count
    FROM issues i
    JOIN issue_categories ic ON i.category_id = ic.id
    GROUP BY ic.name
    ORDER BY issue_count DESC
";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $frequent_issues[] = $row;
        }
    }
    // If num_rows is 0, $frequent_issues array will remain empty, handled in HTML.
} else {
    // Error in query execution
    $page_error_message = "Error fetching frequent issues report: " . htmlspecialchars($conn->error);
}

$conn->close();

// Include header template
require_once '../templates/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>Most Frequent Issue Categories Report</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($page_error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $page_error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($frequent_issues) && empty($page_error_message)): ?>
            <div class="alert alert-info" role="alert">
                No issues logged yet, so no frequency data is available.
            </div>
        <?php elseif (!empty($frequent_issues)): ?>
            <p>This report shows the issue categories ranked by the number of times they have been reported.</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Rank</th>
                            <th>Issue Category Name</th>
                            <th>Number of Occurrences</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($frequent_issues as $issue_data): ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo htmlspecialchars($issue_data['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($issue_data['issue_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
         <hr>
        <a href="../index.php" class="btn btn-secondary">Back to Home</a>
    </div>
</div>

<?php
// Include footer template
require_once '../templates/footer.php';
?>
