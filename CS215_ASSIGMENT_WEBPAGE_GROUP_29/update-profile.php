<?php
// update_profile.php — AJAX endpoint: validate, update DB, return JSON
session_start();

header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit();
}

require 'db.php';

$user_id  = $_SESSION['user_id'];
$errors   = [];
$username = trim($_POST['username'] ?? '');
$dob      = trim($_POST['dob']      ?? '');

// ── Validation ───────────────────────────────────────────────────────────────
if (empty($username)) {
    $errors['username'] = "Username is required.";
} elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
    $errors['username'] = "Username must be 3-50 characters: letters, digits, or underscores only.";
}

if (empty($dob)) {
    $errors['dob'] = "Date of birth is required.";
} elseif (strtotime($dob) >= time()) {
    $errors['dob'] = "Date of birth must be in the past.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit();
}

// ── Handle profile picture upload ────────────────────────────────────────────
$new_pic  = null;
$pic_sql  = '';

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

if (isset($_POST['remove_picture']) && $_POST['remove_picture'] === '1') {
    $new_pic = '';          // empty string signals "set to NULL"
    $pic_sql = ', profile_picture_url = NULL';
}

// ── Database update ──────────────────────────────────────────────────────────
if ($new_pic !== null && $new_pic !== '') {
    // New picture uploaded
    $stmt = $conn->prepare(
        "UPDATE player SET username = ?, dob = ?, profile_picture_url = ? WHERE user_id = ?"
    );
    $stmt->bind_param("sssi", $username, $dob, $new_pic, $user_id);
} elseif ($new_pic === '') {
    // Remove picture
    $stmt = $conn->prepare(
        "UPDATE player SET username = ?, dob = ?, profile_picture_url = NULL WHERE user_id = ?"
    );
    $stmt->bind_param("ssi", $username, $dob, $user_id);
} else {
    // No picture change
    $stmt = $conn->prepare(
        "UPDATE player SET username = ?, dob = ? WHERE user_id = ?"
    );
    $stmt->bind_param("ssi", $username, $dob, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['username'] = $username;

    // Fetch updated picture for response
    $sel = $conn->prepare("SELECT profile_picture_url FROM player WHERE user_id = ?");
    $sel->bind_param("i", $user_id);
    $sel->execute();
    $sel->bind_result($updated_pic);
    $sel->fetch();
    $sel->close();

    $avatar = !empty($updated_pic) ? $updated_pic : 'images/egg.jpg';

    $stmt->close();
    $conn->close();
    echo json_encode([
        'success'  => true,
        'username' => $username,
        'dob'      => $dob,
        'avatar'   => $avatar,
        'message'  => 'Profile updated successfully!'
    ]);
} else {
    $stmt->close();
    $conn->close();
    echo json_encode([
        'success' => false,
        'errors'  => ['username' => 'Update failed. Username may already be taken.']
    ]);
}
?>