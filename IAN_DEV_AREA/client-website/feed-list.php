<?php require_once "resources/config.php" ?>
<!DOCTYPE html>
<html>
    <?php require_once "resources/includes/header.php" ?>
    <body>
        <?php require_once "resources/includes/nav-bar.php" ?>
        <div id="main">
            <?php 
                if(isset($_GET["id"])){
                    if($_GET["id"] == "0"){
                        // Create Cache
                        if(!isset($_SESSION["cache-array"][$_GET["id"]])){
                            echo "Object Cached at: ";
                            $data = "<p>This page should only display during testing</p>";
                            $_SESSION["cache-array"][$_GET["id"]] = new feed($_GET["id"], $data);
                        }
                        else{
                            echo "Cache already exists as of: ";
                        }

                        echo $_SESSION["cache-array"][$_GET["id"]]->get_cache_time();
                        echo "<br>";
                        echo $_SESSION["cache-array"][$_GET["id"]]->get_data();
                    }
                    else{
                        // Create Cache
                        $_SESSION["cache-array"][$_GET["id"]] = time();
                        echo "<h1>ID = " . $_GET["id"] . " Time =  " 
                                . $_SESSION["cache-array"][$_GET["id"]] . "</h1>";
                        // Make RSS request for the feed with the correct ID
                    }
                }
                else{
                    echo 'Error: $_GET was not set.';
                }
            ?>
        </div>
    </body>
</html>