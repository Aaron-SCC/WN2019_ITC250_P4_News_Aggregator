<?php 

    if($_SESSION["login"] == true){
        $loginText = $_SESSION["LAST_ACTIVITY"];
    }
    else{
        $loginText = "Login";
    }

?>

<div id="nav-bar">
    <div id="link-list">
        <a href="index.php" id="banner">Banner</a>
        <a class="nav-link" href="index.php">Home</a>
        <p class="nav-dash"> - </p>
        <a class="nav-link" href="news-list.php">News</a>
    </div>
    <a href="login-page.php" id="login"><?php echo $loginText; ?></a>
</div>