<?php
include 'config.php';

$username = 'username'; // Replace with the desired username
$password = password_hash('password', PASSWORD_DEFAULT); // Replace with the desired password

$sql = "INSERT INTO user_form (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "User registered successfully.";
} else {
  $error_message = "user is registered";
}

$stmt->close();
$conn->close();
?>


<?php
session_start();

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT user_id, username, password FROM user_form WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row["password"];

        if (password_verify($password, $stored_password)) {
            // Authentication successful
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            header("Location: uploadfile.php"); // Redirect to the dashboard
        } else {
            // Authentication failed
            $error_message = "Invalid password.";
        }
    } else {
        // User not found
        $error_message = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="index.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
    </div>
</body>
</html>
