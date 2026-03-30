<?php
// signup.php — Handles account creation
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Retrieve & sanitize inputs ---
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $cpassword= $_POST['cpassword']     ?? '';
    $dob      = trim($_POST['dob']      ?? '');

    // Handle profile picture upload
    $profile_picture_url = null;
    if (isset($_FILES['profilephoto']) && $_FILES['profilephoto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir  = 'images/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext         = pathinfo($_FILES['profilephoto']['name'], PATHINFO_EXTENSION);
        $filename    = uniqid('pfp_', true) . '.' . $ext;
        $destination = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['profilephoto']['tmp_name'], $destination)) {
            $profile_picture_url = $destination;
        }
    }

    // --- Server-side validation ---
    if (empty($username) || empty($email) || empty($password) || empty($cpassword) || empty($dob)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = "Username must be 3-50 characters: letters, digits, or underscores only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (!preg_match('/^(?=.*[^a-zA-Z])\S{6,}$/', $password)) {
        $error = "Password must be at least 6 characters and contain at least one non-letter character.";
    } elseif ($password !== $cpassword) {
        $error = "Passwords do not match.";
    } elseif (strtotime($dob) >= time()) {
        $error = "Date of birth must be in the past.";
    } else {
        // --- Check for duplicate username/email ---
        $stmt = $conn->prepare("SELECT user_id FROM player WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email is already taken.";
        } else {
            // --- Hash password & insert ---
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $insert = $conn->prepare(
                "INSERT INTO player (username, password, email, dob, profile_picture_url)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $insert->bind_param("sssss", $username, $hashed, $email, $dob, $profile_picture_url);

            if ($insert->execute()) {
                $insert->close();
                $conn->close();
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <title>Game Center: Sign Up</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
    <header>
        <a href="signup.php" class="anchor-hover" id="logo-navigate">
            <img id="logo" src="images/Logo2.png" alt="LogoAndName"/>
        </a>
        <a href="login.php" class="anchor-hover user-navigate">Login</a>
    </header>

    <aside id="right-flex"></aside>
    <aside id="left-flex"></aside>

    <main>
        <form class="auth-form" action="signup.php" method="post" enctype="multipart/form-data" id="signup_form">
            <h1>Sign Up</h1>

            <?php if (!empty($error)): ?>
                <p class="error_visible" style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <div class="form-input-grid">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"/>
                </div>
                <div class="error_hidden">&nbsp;</div>

                <div>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"/>
                </div>
                <div class="error_hidden">&nbsp;</div>

                <div>
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob"
                           value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>"/>
                </div>
                <div class="error_hidden">&nbsp;</div>

                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"/>
                </div>
                <div class="error_hidden">&nbsp;</div>

                <div>
                    <label for="cpassword">Confirm Password</label>
                    <input type="password" id="cpassword" name="cpassword"/>
                </div>
                <div class="error_hidden">&nbsp;</div>

                <div>
                    <label for="profilephoto">Profile Picture</label>
                    <input type="file" id="profilephoto" name="profilephoto" accept="image/*"/>
                </div>
                <div class="error_hidden">&nbsp;</div>
            </div>

            <div class="submit">
                <input class="button-hover" type="submit" value="Sign Up"/>
            </div>
        </form>

        <div class="form-note">
            <p>Already have an account? <a href="login.php">Log in!</a></p>
        </div>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
    <script src="js/eventhandler.js"></script>
    <script src="js/register_signup.js"></script>
</body>
</html>