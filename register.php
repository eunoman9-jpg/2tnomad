<?php
session_start();

ini_set('display_errors', 1); error_reporting(E_ALL);

require 'dbconn.php';

$db = new Database();

if (isset($_POST['signup'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Validation
    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    if (strlen($password) < 6) {
        die("Password must be at least 6 characters");
    }

    // ✅ Check if email exists
    $stmt = $db->conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Email already registered");
    }

    // ✅ Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert user
    $stmt = $db->conn->prepare(
        "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())"
    );
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        $db->conn->commit();
        // Auto login after signup
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $name;

        header("Location: index.php");
        exit();

    } else {
        echo "Error: " . $db->conn->error;
    }
}

include_once("include/header.php");

?>

<form method="POST" action="register.php" class="signup-form">
    <h2>Create Account</h2>

    <label for="name">Name</label>
    <input type="text" name="name" placeholder="Full Name" required>

    <label for="email">Email</label>
    <input type="email" name="email" placeholder="Email" required>

    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Password" required>

    <label for="password1">Confirm Password</label>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>

    <button type="submit" name="signup">Sign Up</button>

    <p>Already have an account? <a href="login.php">Login</a></p>
</form>

<?php
include_once("include/footer.php")
?>
