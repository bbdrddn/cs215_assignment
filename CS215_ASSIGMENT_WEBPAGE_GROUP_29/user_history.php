<?php
// user_history.php — Show match history for logged-in user
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch match history with game title and opponent username
$stmt = $conn->prepare("
    SELECT g.game_title,
           uh.match_date,
           p.username AS opponent_name,
           uh.outcome
    FROM user_history uh
    JOIN game g   ON uh.game_id    = g.game_id
    LEFT JOIN player p ON uh.opponent_id = p.user_id
    WHERE uh.user_id = ?
    ORDER BY uh.match_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <title>Game Center: Game History</title>
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
            <h1 class="user-dashboard-title">GAME HISTORY</h1>
            <h3 class="smaller-title-heading">
                Player: <?php echo htmlspecialchars($_SESSION['username']); ?>
            </h3>
        </div>

        <div class="game-history-grid">
            <table class="game-history-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Date Played</th>
                        <th>Opponent</th>
                        <th>Outcome</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows === 0): ?>
                    <tr><td colspan="4" style="text-align:center;">No match history found.</td></tr>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $outcome_class = ($row['outcome'] === 'W') ? 'match-win' : 'match-loss';
                            $outcome_text  = ($row['outcome'] === 'W') ? 'Win' : 'Loss';
                            $opponent      = $row['opponent_name'] ?? 'N/A';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['game_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['match_date']); ?></td>
                            <td><?php echo htmlspecialchars($opponent); ?></td>
                            <td class="<?php echo $outcome_class; ?>"><?php echo $outcome_text; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
</body>
</html>