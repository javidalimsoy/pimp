<?php
include '../src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle avatar upload
    $avatar_dir = __DIR__ . "/uploads/";
    $avatar_file = $avatar_dir . basename($_FILES["avatar"]["name"]);
    $avatar_url = "uploads/default-avatar.png"; // Default avatar

    if (!is_dir($avatar_dir)) {
        mkdir($avatar_dir, 0777, true); // Create uploads directory if it doesn't exist
    }

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar_file)) {
        $avatar_url = "uploads/" . basename($_FILES["avatar"]["name"]);
    }

    // Insert user into database
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password, avatar_url)
        VALUES (:username, :email, :password, :avatar_url)
    ");
    try {
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'avatar_url' => $avatar_url
        ]);

        // Redirect to honeypot page (IDOR vulnerability mimic)
        $user_id = $conn->lastInsertId();
        header("Location: user.php?id=$user_id");
        exit;
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Register</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="avatar">Upload Avatar:</label>
        <input type="file" id="avatar" name="avatar" accept="image/*">

        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
