<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST">
        <select name="choice" onchange="this.form.submit()">
            <option value="">Select...</option>
            <option value="1" <?php if(isset($_POST['choice']) && $_POST['choice'] == '1') echo 'selected'; ?>>1</option>
            <option value="2" <?php if(isset($_POST['choice']) && $_POST['choice'] == '2') echo 'selected'; ?>>2</option>
        </select>
    </form>

    <?php 
        if (isset($_POST['choice'])) {
            $selection = $_POST['choice'];
            if ($selection == "1") {
                echo "Tanav";
            } elseif ($selection == "2") {
                echo "Bansal";
            }
        }
    ?>
</body>
</html>