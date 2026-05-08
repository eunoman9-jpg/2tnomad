<?php
session_start();

require 'dbconn.php'; // your DB class

$db = new Database();

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement (SECURE)
    $stmt = $db->conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {

            // Store session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: index.php"); // redirect after login
            exit();
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
}

include_once("include/header.php");
?>


<form method="POST" action="login.php" class="login-form">
    <h2>Login</h2>

    <input type="email" name="email" placeholder="Email" value="" required>
    <input type="password" name="password" placeholder="Password" value="" required>

    <button type="submit" name="login">Login</button>

    <div style="text-align: center;">
        <p>Don't have an account? <span><a href="register.php">Sign Up</a></span></p>
    </div>
</form>



<?php
include_once("include/footer.php");
?>