<?php

// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'craft_a_interac';

// Connect to database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get integrations
function get_integrations() {
    global $conn;
    $sql = "SELECT * FROM integrations";
    $result = $conn->query($sql);
    $integrations = array();
    while($row = $result->fetch_assoc()) {
        $integrations[] = $row;
    }
    return $integrations;
}

// Function to add integration
function add_integration($name, $description, $api_key) {
    global $conn;
    $sql = "INSERT INTO integrations (name, description, api_key) VALUES ('$name', '$description', '$api_key')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to delete integration
function delete_integration($id) {
    global $conn;
    $sql = "DELETE FROM integrations WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to update integration
function update_integration($id, $name, $description, $api_key) {
    global $conn;
    $sql = "UPDATE integrations SET name='$name', description='$description', api_key='$api_key' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to get integration by ID
function get_integration_by_id($id) {
    global $conn;
    $sql = "SELECT * FROM integrations WHERE id=$id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Create integration table if not exists
$sql = "CREATE TABLE IF NOT EXISTS integrations (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    api_key VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_integration'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $api_key = $_POST['api_key'];
        if (add_integration($name, $description, $api_key)) {
            echo "Integration added successfully!";
        } else {
            echo "Failed to add integration.";
        }
    } elseif (isset($_POST['update_integration'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $api_key = $_POST['api_key'];
        if (update_integration($id, $name, $description, $api_key)) {
            echo "Integration updated successfully!";
        } else {
            echo "Failed to update integration.";
        }
    } elseif (isset($_POST['delete_integration'])) {
        $id = $_POST['id'];
        if (delete_integration($id)) {
            echo "Integration deleted successfully!";
        } else {
            echo "Failed to delete integration.";
        }
    }
}

// Display integrations
$integrations = get_integrations();
?>

<!-- HTML and CSS for the UI -->
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .integration-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .integration-list li {
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }
    .integration-list li:hover {
        background-color: #f0f0f0;
    }
    .integration-list li:last-child {
        border-bottom: none;
    }
    .integration-form {
        width: 50%;
        margin: 40px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Integration list -->
<h1>Integrations</h1>
<ul class="integration-list">
    <?php foreach ($integrations as $integration) { ?>
        <li>
            <h2><?php echo $integration['name']; ?></h2>
            <p><?php echo $integration['description']; ?></p>
            <p>API Key: <?php echo $integration['api_key']; ?></p>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $integration['id']; ?>">
                <input type="submit" name="update_integration" value="Update">
                <input type="submit" name="delete_integration" value="Delete">
            </form>
        </li>
    <?php } ?>
</ul>

<!-- Integration form -->
<h1>Add Integration</h1>
<form method="post" class="integration-form">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name"><br><br>
    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea><br><br>
    <label for="api_key">API Key:</label>
    <input type="text" id="api_key" name="api_key"><br><br>
    <input type="submit" name="add_integration" value="Add Integration">
</form>

<?php $conn->close(); ?>