<?php 
require 'includes/Feed.php';
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials


startSession(); //wrapper for session_start()
//session_destroy();
//die();


# '../' works for a sub-folder.  use './' for the root  
//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
$config->loadhead .= '<link rel="stylesheet" href="../css/celurean-new.css">';

# check variable of item passed in - if invalid data, forcibly redirect back to demo_list.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
    $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
   myRedirect(VIRTUAL_PATH . "/news/index.php");
}

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'News Feed';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central College Student Project: News Feed Aggregator.' . $config->metaDescription;
$config->metaKeywords = 'news, rss, feed'. $config->metaKeywords;

//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';


 //S E S S I O N  D E S T R O Y ////////
if(isset($_POST['destroy']))//when button is clicked,
{//destroy session
    echo '<span class="badge badge-pill badge-danger">Success!</span>';
    session_destroy();
    //refresh to same page
    //unset($_SESSION['timestamp']);        
    header('Location:index.php');


}else{//show form

    $sessionDestroyAction = 
        '
<form action="feed_view.php?id=science" method="post">    
<button class="btn btn-danger" name="destroy" type="submit" value="destroy">session_destroy()</button>
</form>
';
}
// E N D  S E S S I O N  D E S T R O Y ////////



$Feed = new feed($myID); // create feed obect for record 1

/*

echo $Feed->name;
echo '<br>';
echo $Feed->id;
echo '<br>';
echo $Feed->url;
echo '<br>';
echo $Feed->timestamp;
echo '<br>';
//echo $Feed->rawFeed;
echo '<br>';

//test
$myTestResults = $Feed->test();
echo '<pre>' . $myTestResults . '</pre>';
echo '<br>';
    
*/



if(!isset($Feed->timestamp)){//if reord doesn't exist in DB
    $Feed->newToSession(); // add new feed and timestamp to sesion;

    $Feed->sessionToDB(); // also write session to DB
    echo $Feed->timestamp;
    
    //pull DB feed and create html view
    $myViewResult = $Feed->sessionToView();
    $myViewResult['feedState'] = 'fresh';
    
    //print a summary of new feed action
    $myWarning = '
    <div class="alert alert-dismissible alert-danger">
      <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>' . $myViewResult['feedState'] . ' session was created. The Session created time is: ' . date("h:i:sa", $_SESSION['timestamp']) . ' <br>Steps:<br>1.timestamp is not in DB 2.feed will be retreived into session vars  3.session vars will be written to DB 
    4.A result array is created with new data from sessions Var</strong>
    ' . $sessionDestroyAction . '</div> 
    '; 
}
elseif(isset($Feed->timestamp)){//if record exists in DB
    echo '<br>';
    if(time() >= $_SESSION['refreshTime']) 
    {
        $myWarning = '
        <div "alert alert-dismissible alert-warning">
          <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Time to refresh!!!!! Time to refresh Steps:<br> 1.DB data will be NULLed 2.sesion destroyed 3.refresh window</strong>
        </div> 
        ';
        
        $Feed->clearDbCache(); //clear DB data
        session_destroy(); //clear cache
        //header("Refresh:0"); // refresh page
        echo '<script>window.location.reload()</script>'; //refresh page
    }
    
    //*************Take the existing feed and create view*****************//
    //pull feed from session and create html view
    $myViewResult = $Feed->sessionToView();
    $myViewResult['feedState'] = 'Cached';
    
    $myWarning = '
    <div class="alert alert-dismissible alert-danger">
      <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>' . $myViewResult['feedState'] . ' Session. Session was created at '. date("h:i:sa", $_SESSION['timestamp']) . ' seconds. The Session will refresh at: ' . date("h:i:sa", $_SESSION['refreshTime']) . ' Steps:<br> 1.get existing session values and output html view </strong>
    ' . $sessionDestroyAction . '</div> 
    ';
    
}



# END CONFIG AREA ---------------------------------------------------------- 
get_header(); #defaults to header_inc.php
    
 
//****** helpful feed warnings *******//
//print a summary of new feed action
//echo $myNewWarning;

//print a summary of new feed action
echo $myWarning;
?>

    
<h1>
    <?php 
    //echo heading
    //var_dump($myFeedView['feedState']);    
    echo $Feed->name . ' ' . $config->titleTag;
    ?>
</h1>

    <style>
    .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
            -ms-flex-direction: column;
                flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.25rem;
      }
      
    .card-title {
        margin-bottom: 0.75rem;
      }
      
    .mb-3,
    .my-3 {
        margin-bottom: 1rem !important;
      }
    </style>
    

<?php    
//show feed
echo $myViewResult['result']; 


get_footer(); #defaults to footer_inc.php

?>