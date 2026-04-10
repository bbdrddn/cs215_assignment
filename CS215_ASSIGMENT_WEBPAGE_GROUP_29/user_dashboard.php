<?php
// user_dashboard.php — View/update profile (AJAX-based; no full-page form processing)
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// ── DELETE ACCOUNT (non-AJAX; destructive action keeps full-page POST) ───────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $del_hist = $conn->prepare("DELETE FROM user_history WHERE user_id = ?");
    $del_hist->bind_param("i", $user_id);
    $del_hist->execute();
    $del_hist->close();

    $del_user = $conn->prepare("DELETE FROM player WHERE user_id = ?");
    $del_user->bind_param("i", $user_id);
    $del_user->execute();
    $del_user->close();
    $conn->close();

    session_destroy();
    header("Location: login.php");
    exit();
}

// ── RETRIEVE current user data ───────────────────────────────────────────────
$stmt = $conn->prepare("SELECT username, dob, profile_picture_url FROM player WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($db_username, $db_dob, $db_pic);
$stmt->fetch();
$stmt->close();
$conn->close();

$avatar = !empty($db_pic) ? htmlspecialchars($db_pic) : 'images/egg.jpg';
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <title>Game Center: User Dashboard</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
    <header>
        <a href="game_dashboard.php" class="anchor-hover" id="logo-navigate">
            <img id="logo" src="images/Logo2.png" alt="LogoAndName"/>
        </a>
        <a href="user_dashboard.php" class="anchor-hover user-navigate">Profile</a>
        <a href="user_history.php" class="anchor-hover user-navigate">History</a>
        <a href="logout.php" class="anchor-hover user-navigate">Logout</a>
    </header>

    <aside id="right-flex"></aside>
    <aside id="left-flex"></aside>

    <main class="user-dashboard">
        <div class="title-heading">
            <h1 class="user-dashboard-title">USER DASHBOARD</h1>
        </div>

        <!-- AJAX feedback messages (hidden by default) -->
        <p id="ajax-error"   class="error_hidden"  style="color:red;   text-align:center;">&nbsp;</p>
        <p id="ajax-success" class="error_hidden"  style="color:green; text-align:center;">&nbsp;</p>

        <form id="user_info_form" action="update_profile.php" method="post" enctype="multipart/form-data">

            <div class="smaller-title-heading">
                <h2>Profile Picture</h2>
                <div class="profile-photo-grid">
                    <img src="<?php echo $avatar ?>" id="profile-photo-frame" alt="profile avatar"/>
                    <div class="profile-photo-options">
                        <p>Upload a clear profile picture (preferably 200x200 pixels).</p>
                        <div class="profile-photo-buttons">
                            <button type="button" class="button-hover" id="upload-button">Upload Profile Picture</button>
                            <button type="button" class="button-hover cautious-button" id="remove-button">Remove Profile Picture</button>
                        </div>
                        <div class="error_hidden">&nbsp;</div>
                    </div>
                </div>
                <!-- hidden file input triggered by Upload button -->
                <input type="file" class="error_hidden" id="file-upload-input" name="profilephoto" accept="image/*"/>
                <!-- hidden flag set when user clicks Remove -->
                <input type="hidden" id="remove-picture-flag" name="remove_picture" value="0"/>

                <h2>User Information</h2>
                <div class="info-input-grid">
                    <div class="input-fields">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username"
                               value="<?php echo htmlspecialchars($db_username); ?>"/>
                    </div>
                    <div class="error_hidden">&nbsp;</div>

                    <div class="input-fields">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                               value="<?php echo htmlspecialchars($db_dob); ?>"/>
                    </div>
                    <div class="error_hidden">&nbsp;</div>
                </div>
            </div>

            <div class="smaller-title-heading">
                <h2>Game History</h2>
                <div>
                    <p>Game history can be accessed by clicking the button below.</p>
                    <a href="user_history.php" class="user-dashboard-button button-hover">Access History Page</a>
                </div>

                <h2>Account Deletion</h2>
                <div>
                    <p>Clicking the button below will permanently remove your account.</p>
                    <button type="submit" name="delete_account"
                            class="user-dashboard-button button-hover cautious-button"
                            onclick="return confirm('Are you sure? This cannot be undone.');">
                        Delete This Account
                    </button>
                </div>
            </div>

            <div class="smaller-title-heading">
                <div class="save-button-grid">
                    <button type="reset" class="user-dashboard-button button-hover" id="reset-button">Reset</button>
                    <button type="button" id="save-button" class="user-dashboard-button button-hover green-button">Save Changes</button>
                </div>
            </div>

        </form>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
    <script src="js/eventhandler.js"></script>
    <script src="js/register_userdashboard.js"></script>
</body>
</html>