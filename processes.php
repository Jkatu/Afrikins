<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Connect to database
$db = new SQLite3('E:\SHINA app\classdb.db');

// 2. DEBUG: Show existing tables and structure
echo "<pre>";
echo "Existing tables:\n";
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
while ($table = $tables->fetchArray(SQLITE3_ASSOC)) {
    echo "- ".$table['name']."\n";
    // Show columns for each table
    $columns = $db->query("PRAGMA table_info(".$table['name'].")");
    while ($col = $columns->fetchArray(SQLITE3_ASSOC)) {
        echo "  - ".$col['name']." (".$col['type'].")\n";
    }
}
echo "</pre>";

// 3. Verify/Create table with EXACT correct structure
$db->exec("DROP TABLE IF EXISTS users"); // Only for testing - remove in production
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
)");

// 4. Handle form submission
if(isset($_POST['register'])) {
    try {
        // Get form data
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Validate
        if(empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        }
        
        // Check for existing user
        $check = $db->prepare("SELECT 1 FROM users WHERE username = :username OR email = :email");
        $check->bindValue(':username', $username, SQLITE3_TEXT);
        $check->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $check->execute();
        
        if($result->fetchArray()) {
            throw new Exception("Username or email already exists");
        }
        
        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);
        
        if(!$stmt->execute()) {
            throw new Exception("Registration failed: ".$db->lastErrorMsg());
        }
        
        header("Location: success.html");
        exit;
        
    } catch (Exception $e) {
        die("Error: ".$e->getMessage());
    }
}
?>

<!-- Registration Form -->
<h2>Register</h2>
<form method="post">
    <input type="text" name="username" required placeholder="Username">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit" name="register">Register</button>
</form>