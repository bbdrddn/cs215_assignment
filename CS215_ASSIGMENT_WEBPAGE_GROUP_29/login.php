<?php
// login.php — Handles authentication and session creation
session_start();

// If already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: game_dashboard.php");
    exit();
}

require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']      ?? '';

    // Server-side validation
    if (empty($username) || empty($password)) {
        $error = "Invalid username or password.";
    } else {
        // Look up user by username
        $stmt = $conn->prepare("SELECT user_id, username, password FROM player WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $db_username, $db_password);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        if ($user_id && password_verify($password, $db_password)) {
            // Valid — create session
            $_SESSION['user_id']  = $user_id;
            $_SESSION['username'] = $db_username;
            header("Location: game_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <title>Game Center: Login</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
    <header>
        <a href="signup.php" id="logo-navigate">
            <img id="logo" src="images/Logo2.png" alt="LogoAndName"/>
        </a>
        <a href="signup.php" class="anchor-hover user-navigate">Sign Up</a>
    </header>

    <aside id="right-flex"></aside>
    <aside id="left-flex"></aside>

    <main>
        <form class="auth-form" action="login.php" method="post" id="form_login">
            <h1>Log In</h1>

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
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"/>
                </div>
                <div class="error_hidden">&nbsp;</div>
            </div>

            <div id="savecredentials">
                <input type="checkbox" name="rememberme" id="rememberme"/>
                <label for="rememberme">Remember Me</label>
            </div>

            <div class="submit">
                <input class="button-hover" type="submit" value="Login" id="submit_input"/>
            </div>
        </form>

        <div class="form-note">
            <p>Don't have an account? <a href="signup.php">Sign Up!</a></p>
        </div>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
    <script src="js/eventhandler.js"></script>
    <script src="js/register_login.js"></script>
</body>
</html>