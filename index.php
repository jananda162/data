<?php
// Include the database connection script
require_once 'php/includes/db_connect.php'; // MODIFIED PATH

$message = '';
$error_message = ''; // For general page errors, not form submission messages

// Initialize arrays for dropdowns
$users = [];
$departments = [];
$issue_categories = [];

// Fetch users for dropdown
$user_sql = "SELECT id, name, username FROM users ORDER BY name ASC";
$user_result = $conn->query($user_sql);
if ($user_result && $user_result->num_rows > 0) {
    while ($row = $user_result->fetch_assoc()) {
        $users[] = $row;
    }
} else if (!$user_result) {
    $error_message .= "Error fetching users: " . htmlspecialchars($conn->error) . "<br>";
}

// Fetch departments for dropdown
$dept_sql = "SELECT id, name FROM departments ORDER BY name ASC";
$dept_result = $conn->query($dept_sql);
if ($dept_result && $dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
} else if (!$dept_result) {
    $error_message .= "Error fetching departments: " . htmlspecialchars($conn->error) . "<br>";
}

// Fetch issue categories for dropdown
$cat_sql = "SELECT id, name FROM issue_categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $issue_categories[] = $row;
    }
} else if (!$cat_result) {
    $error_message .= "Error fetching issue categories: " . htmlspecialchars($conn->error) . "<br>";
}

// --- Handle Form Submission (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's for the main issue logging form, not modal forms (though modals use AJAX)
    // This simple check might need to be more robust if other POSTs are expected on index.php
    if (isset($_POST['issue_description'])) { 
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $device_id = isset($_POST['device_id']) && !empty($_POST['device_id']) ? (int)$_POST['device_id'] : NULL; // Allow NULL
        $issue_description = isset($_POST['issue_description']) ? trim($_POST['issue_description']) : '';

        // Validate input
        if (empty($user_id) || empty($department_id) || empty($category_id) || empty($issue_description)) {
            $message = "Error: User, Department, Category, and Description are required fields.";
        } else {
            // Prepare an SQL statement
            $stmt = $conn->prepare("INSERT INTO issues (user_id, department_id, category_id, device_id, description) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiiis", $user_id, $department_id, $category_id, $device_id, $issue_description);
                if ($stmt->execute()) {
                    $message = "Success: New issue logged successfully.";
                    $_POST = array(); 
                    $selected_device_id = '';
                    $selected_device_info = 'None selected. Use search.';
                } else {
                    $message = "Error: Could not log issue. " . htmlspecialchars($stmt->error);
                }
                $stmt->close();
            } else {
                $message = "Error: Could not prepare the SQL statement. " . htmlspecialchars($conn->error);
            }
        }
    }
}

// --- Pre-fill device information if coming from search_devices.php (now search_devices.php in php/ folder) ---
$selected_device_id = '';
$selected_device_info = 'None selected. Use search.';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['device_id'])) {
    $selected_device_id = (int)$_GET['device_id'];
    if (isset($_GET['device_info'])) {
        $selected_device_info = htmlspecialchars($_GET['device_info']);
    } else {
        // Fetch device info if only ID is provided
        $dev_stmt = $conn->prepare("SELECT device_type, serial_number FROM devices WHERE id = ?");
        if ($dev_stmt) {
            $dev_stmt->bind_param("i", $selected_device_id);
            $dev_stmt->execute();
            $dev_result = $dev_stmt->get_result();
            if ($dev_row = $dev_result->fetch_assoc()) {
                $selected_device_info = htmlspecialchars($dev_row['device_type'] . ' - ' . $dev_row['serial_number']);
            } else {
                $selected_device_id = ''; 
                $selected_device_info = 'Device not found. Please search again.';
            }
            $dev_stmt->close();
        }
    }
}

$conn->close();

require_once 'templates/header.php'; // MODIFIED PATH
?>

<div class="card">
    <div class="card-header">
        <h2>Log New IT Issue</h2>
    </div>
    <div class="card-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($message) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['issue_description']) /* Check if it was main form submission */ ): ?>
            <div class="alert <?php echo strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="post" id="logIssueForm"> <!-- MODIFIED PATH (action to self) -->
            <div class="form-group">
                <label for="user_id">IT Assistant Reporting:</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name'] . ' (' . $user['username'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="department_id">Department:</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo (isset($_POST['department_id']) && $_POST['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small><button type="button" class="btn btn-sm btn-outline-success mt-1" data-toggle="modal" data-target="#addDepartmentModal">Add New Department</button></small>
            </div>

            <div class="form-group">
                <label for="category_id">Issue Category:</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($issue_categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small><button type="button" class="btn btn-sm btn-outline-success mt-1" data-toggle="modal" data-target="#addCategoryModal">Add New Category</button></small>
            </div>
            
            <div class="form-group border p-3 rounded bg-light">
                <label for="device_serial_search">Device Serial Number (Optional):</label>
                <input type="text" id="device_serial_search" name="device_serial_search_display" class="form-control mb-2" placeholder="Enter serial to search" value="<?php echo isset($_POST['device_serial_search_display']) ? htmlspecialchars($_POST['device_serial_search_display']) : ''; ?>">
                
                <button type="button" id="ajaxSearchDeviceBtn" class="btn btn-info btn-sm">Search Device (AJAX)</button>
                
                <input type="hidden" id="device_id" name="device_id" value="<?php echo htmlspecialchars($selected_device_id); ?>">
                <div id="selected_device_display" class="mt-2 p-2 bg-white border rounded <?php echo empty($selected_device_id) ? 'text-muted' : 'text-success font-weight-bold'; ?>">
                    Selected Device: <span id="selected_device_text"><?php echo htmlspecialchars($selected_device_info); ?></span>
                </div>
                <div id="device_search_results_ajax" class="mt-2">
                    <!-- AJAX device search results will be populated here -->
                </div>
            </div>

            <div class="form-group">
                <label for="issue_description">Issue Description:</label>
                <textarea id="issue_description" name="issue_description" class="form-control" required><?php echo isset($_POST['issue_description']) ? htmlspecialchars($_POST['issue_description']) : ''; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Issue</button>
        </form>
        <!-- Removed "Back to Home" link as this IS the home page now -->
    </div>
</div>

<script>
    // This JS function is not used by AJAX buttons anymore, but can be kept for reference
    // function searchDeviceSimple() { 
    //     const serialNumber = document.getElementById('device_serial_search').value;
    //     if (serialNumber.trim() === "") {
    //         alert("Please enter a serial number to search.");
    //         return;
    //     }
    //     // When index.php is the main page, search_devices.php is in php/
    //     const searchUrl = `php/search_devices.php?search_term=${encodeURIComponent(serialNumber)}`;
    //     window.open(searchUrl, '_blank'); 
    // }
</script>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modalAddDepartmentForm">
                    <div class="form-group">
                        <label for="modal_department_name">Department Name:</label>
                        <input type="text" class="form-control" id="modal_department_name" name="department_name" required>
                    </div>
                    <div id="modalDepartmentMessage" class="mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modalSubmitDepartment">Add Department</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Issue Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Issue Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modalAddCategoryForm">
                    <div class="form-group">
                        <label for="modal_category_name">Category Name:</label>
                        <input type="text" class="form-control" id="modal_category_name" name="category_name" required>
                    </div>
                    <div id="modalCategoryMessage" class="mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modalSubmitCategory">Add Category</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php'; // MODIFIED PATH
?>

<script>
$(document).ready(function() {
    // Helper function to display messages in modals
    function showModalMessage(selector, message, isSuccess) {
        $(selector).html(
            `<div class="alert ${isSuccess ? 'alert-success' : 'alert-danger'}">${message}</div>`
        );
    }
    
    // Helper function to display messages on the main page
    function showPageMessage(message, isSuccess) {
        const pageMessageDiv = $('.card-body').find('.alert').first(); 
        if (pageMessageDiv.length && pageMessageDiv.closest('.modal').length === 0) { // Ensure it's not a modal alert
             pageMessageDiv.removeClass('alert-success alert-danger').addClass(isSuccess ? 'alert-success' : 'alert-danger').html(message).show();
        } else {
            const newAlert = $(`<div class="alert ${isSuccess ? 'alert-success' : 'alert-danger'}">${message}</div>`);
            // Prepend to card-body, but make sure it's not inside a modal if one is also open
             $('.card:not(.modal .card) .card-body').first().prepend(newAlert);
        }
         $('html, body').animate({ scrollTop: $('.card-body').not('.modal .card-body').first().offset().top }, 'slow');
    }

    // Add Department via AJAX Modal
    $('#modalSubmitDepartment').on('click', function() {
        const deptName = $('#modal_department_name').val().trim();
        if (!deptName) {
            showModalMessage('#modalDepartmentMessage', 'Department name is required.', false);
            return;
        }

        $.ajax({
            url: 'php/add_department.php', // MODIFIED PATH
            type: 'POST',
            data: { department_name: deptName },
            dataType: 'json',
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function(response) {
                if (response.success) {
                    $('#department_id').append(new Option(response.name, response.id, true, true)).trigger('change');
                    $('#addDepartmentModal').modal('hide');
                    $('#modal_department_name').val(''); 
                    $('#modalDepartmentMessage').html(''); 
                    showPageMessage('Department "' + escapeHtml(response.name) + '" added successfully!', true);
                } else {
                    showModalMessage('#modalDepartmentMessage', response.message || 'Could not add department.', false);
                }
            },
            error: function() {
                showModalMessage('#modalDepartmentMessage', 'An error occurred. Please try again.', false);
            }
        });
    });

    // Add Issue Category via AJAX Modal
    $('#modalSubmitCategory').on('click', function() {
        const catName = $('#modal_category_name').val().trim();
        if (!catName) {
            showModalMessage('#modalCategoryMessage', 'Category name is required.', false);
            return;
        }

        $.ajax({
            url: 'php/add_issue_category.php', // MODIFIED PATH
            type: 'POST',
            data: { category_name: catName },
            dataType: 'json',
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function(response) {
                if (response.success) {
                    $('#category_id').append(new Option(response.name, response.id, true, true)).trigger('change');
                    $('#addCategoryModal').modal('hide');
                    $('#modal_category_name').val(''); 
                    $('#modalCategoryMessage').html(''); 
                    showPageMessage('Category "' + escapeHtml(response.name) + '" added successfully!', true);
                } else {
                    showModalMessage('#modalCategoryMessage', response.message || 'Could not add category.', false);
                }
            },
            error: function() {
                showModalMessage('#modalCategoryMessage', 'An error occurred. Please try again.', false);
            }
        });
    });
    
    // AJAX Device Search
    $('#ajaxSearchDeviceBtn').on('click', function() {
        const serialNumber = $('#device_serial_search').val().trim();
        if (serialNumber === "") {
            alert("Please enter a serial number to search.");
            $('#device_search_results_ajax').html(''); 
            return;
        }

        $.ajax({
            url: 'php/search_devices.php', // MODIFIED PATH
            type: 'GET',
            data: { search_term: serialNumber },
            dataType: 'json',
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function(response) {
                $('#device_search_results_ajax').html(''); 
                if (response.success && response.devices.length > 0) {
                    let resultsHtml = '<ul class="list-group mt-2">';
                    response.devices.forEach(function(device) {
                        resultsHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                            ${escapeHtml(device.device_type)} - ${escapeHtml(device.serial_number)}
                                            <button type="button" class="btn btn-sm btn-primary select-device-btn" data-device-id="${device.id}" data-device-info="${escapeHtml(device.device_type)} - ${escapeHtml(device.serial_number)}">Select</button>
                                        </li>`;
                    });
                    resultsHtml += '</ul>';
                    $('#device_search_results_ajax').html(resultsHtml);
                } else if (response.success && response.devices.length === 0) {
                    $('#device_search_results_ajax').html('<p class="text-muted mt-2">No devices found matching that serial number.</p>');
                } 
                else {
                     $('#device_search_results_ajax').html(`<p class="text-danger mt-2">${response.message || 'Error searching for devices.'}</p>`);
                }
            },
            error: function() {
                $('#device_search_results_ajax').html('<p class="text-danger mt-2">An error occurred while searching for devices.</p>');
            }
        });
    });

    // Handle device selection from AJAX search results
    $(document).on('click', '.select-device-btn', function() {
        const deviceId = $(this).data('device-id');
        const deviceInfo = $(this).data('device-info');

        $('#device_id').val(deviceId);
        $('#selected_device_text').text(deviceInfo);
        $('#selected_device_display').removeClass('text-muted').addClass('text-success font-weight-bold');
        $('#device_search_results_ajax').html(''); 
        $('#device_serial_search').val(deviceInfo.split(' - ')[1]); 
    });
    
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') {
            return ''; // Or handle as an error, or convert to string
        }
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    // Clear modal messages on close
    $('#addDepartmentModal, #addCategoryModal').on('hidden.bs.modal', function () {
        $(this).find('.alert').remove(); 
        $(this).find('form')[0].reset(); 
    });

    // If coming back with GET params for device (from non-AJAX search, though that path is less likely now)
    const urlParams = new URLSearchParams(window.location.search);
    const deviceIdFromGet = urlParams.get('device_id');
    const deviceInfoFromGet = urlParams.get('device_info');
    if (deviceIdFromGet) {
        $('#device_id').val(deviceIdFromGet);
        $('#selected_device_text').text(deviceInfoFromGet ? escapeHtml(deviceInfoFromGet) : `ID: ${deviceIdFromGet} (Info not provided)`);
        $('#selected_device_display').removeClass('text-muted').addClass('text-success font-weight-bold');
        // Optionally, clear the GET parameters from URL
        // window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
