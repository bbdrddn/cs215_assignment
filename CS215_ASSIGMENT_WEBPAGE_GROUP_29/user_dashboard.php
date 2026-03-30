<?php
// user_dashboard.php — View/update profile, delete account
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = '';

// ── DELETE ACCOUNT ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Delete user_history rows first (foreign key), then player
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

// ── UPDATE PREFERENCES ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $username = trim($_POST['username'] ?? '');
    $dob      = trim($_POST['dob']      ?? '');

    // Server-side validation
    if (empty($username) || empty($dob)) {
        $error = "Username and date of birth are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = "Username must be 3-50 characters: letters, digits, or underscores only.";
    } elseif (strtotime($dob) >= time()) {
        $error = "Date of birth must be in the past.";
    } else {
        // Handle optional new profile picture
        $pic_sql   = '';
        $new_pic   = null;

        if (isset($_FILES['profilephoto']) && $_FILES['profilephoto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'images/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext         = pathinfo($_FILES['profilephoto']['name'], PATHINFO_EXTENSION);
            $filename    = uniqid('pfp_', true) . '.' . $ext;
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['profilephoto']['tmp_name'], $destination)) {
                $new_pic = $destination;
                $pic_sql = ", profile_picture_url = ?";
            }
        }

        // Remove picture if requested
        if (isset($_POST['remove_picture'])) {
            $new_pic = null;
            $pic_sql = ", profile_picture_url = NULL";
        }

        if ($new_pic !== null) {
            $stmt = $conn->prepare(
                "UPDATE player SET username = ?, dob = ?$pic_sql WHERE user_id = ?"
            );
            $stmt->bind_param("sssi", $username, $dob, $new_pic, $user_id);
        } elseif ($pic_sql === ", profile_picture_url = NULL") {
            $stmt = $conn->prepare(
                "UPDATE player SET username = ?, dob = ?, profile_picture_url = NULL WHERE user_id = ?"
            );
            $stmt->bind_param("ssi", $username, $dob, $user_id);
        } else {
            $stmt = $conn->prepare(
                "UPDATE player SET username = ?, dob = ? WHERE user_id = ?"
            );
            $stmt->bind_param("ssi", $username, $dob, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
        } else {
            $error = "Update failed. Username may already be taken.";
        }
        $stmt->close();
    }
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

        <?php if (!empty($error)): ?>
            <p class="error_visible" style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p style="color:green; text-align:center;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form id="user_info_form" action="user_dashboard.php" method="post" enctype="multipart/form-data">

            <div class="smaller-title-heading">
                <h2>Profile Picture</h2>
                <div class="profile-photo-grid">
                    <img src="<?php echo $avatar; ?>" id="profile-photo-frame" alt="profile avatar"/>
                    <div class="profile-photo-options">
                        <p>Upload a clear profile picture (preferably 200x200 pixels).</p>
                        <div class="profile-photo-buttons">
                            <button type="button" class="button-hover" id="upload-button">Upload Profile Picture</button>
                            <button type="submit" name="remove_picture" class="button-hover cautious-button" id="remove-button">Remove Profile Picture</button>
                        </div>
                        <div class="error_hidden">&nbsp;</div>
                    </div>
                </div>
                <!-- hidden file input triggered by Upload button -->
                <input type="file" class="error_hidden" id="file-upload-input" name="profilephoto" accept="image/*"/>

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
                    <button type="submit" name="save_changes" class="user-dashboard-button button-hover green-button">Save Changes</button>
                </div>
            </div>

        </form>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
    <script src="js/eventhandler.js"></script>
    <script src="js/register_userdashboard.js"></script>
</body>
</html>