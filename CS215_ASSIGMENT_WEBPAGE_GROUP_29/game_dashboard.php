<?php
// game_dashboard.php — Main dashboard (requires login)
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="css/styles.css"/>
    <title>Game Center: Dashboard</title>
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
            <h1 class="user-dashboard-title">GAME MENU</h1>
            <h3 class="smaller-title-heading">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! Play featured games by clicking their thumbnails.
            </h3>
        </div>

        <div class="smaller-title-heading">
            <h2>featured games</h2>
            <div class="featured-games-grid">
                <div class="game-card">
                    <a href="" class="anchor-hover game-thumbnail">
                        <img src="images/game_thumbnails/chess.png" alt="thumbnail for chess game"/>
                    </a>
                    <h3>Chess</h3>
                </div>
            </div>

            <h2>Other games</h2>
            <div class="other-games-grid">
                <div class="game-row">
                    <div class="title-card">
                        <img class="row-icon" src="images/egg.jpg" alt="game icon"/>
                        <h3 class="row-title">Game Title</h3>
                    </div>
                    <a href="" class="anchor-hover green-button play-button">PLAY GAME</a>
                </div>
            </div>
        </div>
    </main>

    <footer>CS215 - Group 29: Bansal, Le, Vi</footer>
</body>
</html>