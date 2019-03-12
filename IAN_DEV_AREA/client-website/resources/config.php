<?php
    define('THIS_PAGE',basename($_SERVER['PHP_SELF']));

    switch(THIS_PAGE){

        case 'index.php':
            $title = "Home Page";
            $logo = 'fa-home';
            $PageID = 'Welcome';
        break;
        
        case 'login-page.php':
            $title = "Login Page";
            $logo = 'fa-pencil-square-o';
            $PageID = 'login-page';
        break;
        
        case 'news-list.php':
            $title = "News";
            $logo = 'fa-pencil-square-o';
            $PageID = 'news-list';
        break;

        default:
            $title = THIS_PAGE;
            $logo = 'fa-home';
            $PageID = 'Welcome';
        
    }

    // Session
    session_start();

    // Function to make sure that all variables are cleared when the session resets
    function session_restart(){
        // Make sure that the objects dont stay on the page
        if(isset($_SESSION["cat-array"])){
            foreach($_SESSION["cat-array"] as $cat){
                $cat = null;
            }
            $_SESSION["cat-array"] = null;
        }
        if(isset($_SESSION["cache-array"])){
            foreach($_SESSION["cache-array"] as $cat){
                $cat = null;
            }
            $_SESSION["cache-array"] = null;
        }
        session_destroy();  
        session_unset();  

    }

    // End session if there is no activity in 1800 seconds (30 minutes)
    if (isset($_SESSION["LAST_ACTIVITY"]) && (time() - $_SESSION["LAST_ACTIVITY"] > 1800)) {
        session_restart();
    }
    // Clear Cache after 600 seconds (10 Minutes)
    $clearTime = 10;
    if(isset($_GET["id"]) && isset($_SESSION["cache-array"])){
        // If any of the pages have been cahced for 600 seconds (10 minutes) clear them
        foreach($_SESSION["cache-array"] as $index => $cache){
            if (isset($_SESSION["LAST_ACTIVITY"]) && (time() - $cache->get_cache_time() > $clearTime)) {
                unset($_SESSION["cache-array"][$index]);
            }
        }
        // If the user is on a feed page, the timer on that page
        if(isset($_SESSION["cache-array"][$_GET["id"]])){
            $_SESSION["cache-array"][$_GET["id"]]->reset_time();
        }
    }
    // update last activity time stamp
    $_SESSION['LAST_ACTIVITY'] = time();
    
    // Make sure that the user is not logged in when the session starts
    if(!isset($_SESSION["login"])){
        $_SESSION["login"] = false;
    }

    // Define objects used for session variables
    class category{
        // Declare Variables
        private $name = "";
        private $id = 0;
        private $feeds = [];

        // Construct Object
        function __construct($inId, $inName){
            $this->id = $inId;
            $this->name = $inName;
        }

        // Get the feed data from the database
        function set_feeds(){
            // Set dummy feed for testing
            $this->feeds[0] = "Dummy Feed";
            /*
                Database Querry
                    SELECT * FROM Feeds WHERE categoryID = $this->id
                
                foreach($response as $feed){
                    $this->feeds[$feed->FeedID] = $feed->FeedName
                }
            */
        }

        // Populate new-list page
        function print_links(){
            echo "<h1>$this->name</h1>";
            foreach($this->feeds as $index => $feed){
                echo "<a href='feed-list.php?id=$index'>$feed</a>";
            }
        }

        // Populate the feed-list page
        function print_feeds(){
            /*
                foreach($feeds as $feed){
                    echo "
                        <div class='container story-container' id='[feedid]'>
                            <h1>[feed name]</h1>
                            <p>[feed discription]</p>
                        </div>
                    "
                }
            */
        }

        // Populate the feed-view page
        function print_feed($inFeedId){
            /*
                $url = $feeds[$inFeedId]->feedURL

                request feed using $url

                display the feed data
            */
        }
    }

    // Object to store RSS feed info
    class feed{
        private $id;
        private $timeStamp;
        private $data;

        function __construct($inId, $inData){
            $this->id = $inId;
            $this->data = $inData;
            $this->timeStamp = time();
        }

        // Get/Set Functions
        function reset_time(){
            $this->timeStamp = time();
        }
        function get_cache_time(){
            return $this->timeStamp;
        }

        function get_data(){
            return $this->data;
        }
    }
    

?>