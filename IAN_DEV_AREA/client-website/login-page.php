
<!DOCTYPE html>
<html>
    <?php require "resources/includes/header.php" ?>
    <?php $_SESSION["login"] = true; ?>
    <body>
        <?php require "resources/includes/nav-bar.php" ?>
        <div id="main">
            <div class="container">
                <h1>Welcome</h1>
                <p>You Have Logged In</p>
                <a href="index.php">Back to Homepage</a>
            </div>
        </div>
    </body>
</html>