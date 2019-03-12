<?php require_once "resources/config.php" ?>
<!DOCTYPE html>
<html>
    <?php require_once "resources/includes/header.php" ?>

    <?php 
        if(!isset($_SESSION["cat-array"])){
            $_SESSION["cat-array"] = [];
            // Create dummy object for testing
            $_SESSION["cat-array"][0] = new category(0, "Dummy Category");
        }
        /*
            Database Querry
                SELECT * FROM Categories 
                (Expecting an array of objects, where each object
                    is a row from the table)

            (Store each row-object as an object in the cat-array session variable)
            foreach($response as $cat){
                $_SESSION("cat-array")[$cat->CategoryID] = new category($cat->CategoryID, $cat->CategoryName)
            }
        */
    ?>

    <body>
        <?php require_once "resources/includes/nav-bar.php" ?>
        <div id="main">
            <div class="container category-container">
                <div class="category-pic"></div>
                <div class="category-text">
                    <?php  
                        foreach($_SESSION["cat-array"] as $cat){
                            $cat->set_feeds();
                            $cat->print_links();
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>