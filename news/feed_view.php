<?php
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
startSession(); //wrapper for session_start()
//session_destroy();
//die();

# '../' works for a sub-folder.  use './' for the root  
//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">';
$config->loadhead .= '<link rel="stylesheet" href="../css/celurean-new.css">';


#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'News Feed';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central College Student Project: News Feed Aggregator.' . $config->metaDescription;
$config->metaKeywords = 'news, rss, feed'. $config->metaKeywords;


# check variable of item passed in - if invalid data, forcibly redirect back to demo_list.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
    $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
   myRedirect(VIRTUAL_PATH . "/news/index.php");
}



if(!isset($_SESSION['arObjs']))
{
    $NewFeed = new Feed();//initialize obj
    $NewFeed->dbToObj($myID);// dump db values into object
    
    if(!isset($NewFeed->timestamp))
    {//get new feed
        $NewFeed->fetchFresh(); 
    }
    
    if(time() < $NewFeed->refreshTime)
    {//get new feed
        $NewFeed->fetchFresh();
    }    
    
    objToSession($NewFeed);//creates session array of objects and appends $Feed to array
    $NewFeed->objToDB(); // updates DB rawfeed and timestamp with object values
    
    $myViewResult = $NewFeed->sessionToView();
    
    $myViewResult['feedState'] = 'fresh';
    $myViewResult['feedTitle'] = $NewFeed->name;
    
    //print a summary of new feed action
    $myWarning = '
    <div class="alert alert-dismissible alert-danger">
      <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>' . $myViewResult['feedState'] . ' session was created.<br>
      Session creation time: ' . date("h:i:sa", $NewFeed->timestamp) . ' <br>
      Session refresh time: ' . date("h:i:sa", $NewFeed->refreshTime) . ' <br>      
      </strong>
    </div> 
    '; 
    
}elseif(isset($_SESSION['arObjs']))
{
    //loop through each object in array
    foreach($_SESSION['arObjs'] as $obj)
    {
        if($obj->id == $myID)
        {//pass values to a new object
            $myObj = $obj;
        }
    }//foreach
    
    //if no feed in DB
    if(!isset($myObj->id))
    {//fetch DB values, if null, create new feed
        $myObj = new Feed();//initialize obj
        $myObj->dbToObj($myID);// dump db values into object
        //get new feed
        $myObj->fetchFresh(); 
        objToSession($myObj);//creates session array of objects and appends $Feed to array
        $myObj->objToDB(); // updates DB rawfeed and timestamp with object values    
    }
    
    if(time() > $myObj->refreshTime)
    {//get a new feed and display        
        $RefreshedFeed = new Feed();;//initialize obj
        //loop through each object in array
        foreach($_SESSION['arObjs'] as $obj)
        {
            if($obj->id == $myID)
            {//pass sessionvalues to a Feed object
                $RefreshedFeed->id = $obj->id;
                $RefreshedFeed->name = $obj->name;
                $RefreshedFeed->url = $obj->url;
                $RefreshedFeed->request = $obj->request;
            }
        }//foreach

        $RefreshedFeed->fetchFresh();
        objToSession($RefreshedFeed);//creates session array of objects and appends $Feed to array        
        $RefreshedFeed->objToDB();// updates DB rawfeed and timestamp with object values
        $myViewResult = $RefreshedFeed->sessionToView();
        
        //print a summary of feed action
        $myViewResult['feedState'] = 'fresh';
        $myViewResult['feedTitle'] = $myObj->name;
        $myWarning = '
        <div class="alert alert-dismissible alert-danger">
          <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>' . $myViewResult['feedState'] . ' session was created.<br>
          Session creation time: ' . date("h:i:sa", $myObj->timestamp) . ' <br>
          Session refresh time: ' . date("h:i:sa", $myObj->refreshTime) . ' <br>      
          </strong>
        </div> 
        '; 
        
    }
    elseif(time() <= $myObj->refreshTime)
    {//
        //load session obj to an obj we can work with
        $CachedFeed = new Feed();//initialize obj
        //loop through each object in array
        foreach($_SESSION['arObjs'] as $obj)
        {
            if($obj->id == $myID)
            {//pass sessionvalues to a Feed object
                $CachedFeed->id = $obj->id;
                $CachedFeed->name = $obj->name;
                $CachedFeed->url = $obj->url;
                $CachedFeed->request = $obj->request;
                $CachedFeed->refreshTime = $obj->refreshTime;
                $CachedFeed->timestamp = $obj->timestamp;

            }
        }//foreach

        $myViewResult = $CachedFeed->sessionToView();
        
        //print a summary of feed action
        $myViewResult['feedState'] = 'cache';
        $myViewResult['feedTitle'] = $CachedFeed->name;
        $myWarning = '
        <div class="alert alert-dismissible alert-danger">
          <button onclick="this.parentElement.style.display = \'none\';" type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>' . $myViewResult['feedState'] . ' session was created.<br>
          Session creation time: ' . date("h:i:sa", $CachedFeed->timestamp) . ' <br>
          Session refresh time: ' . date("h:i:sa", $CachedFeed->refreshTime) . ' <br>      
          </strong>
        </div> 
        '; 
    }
    
    
    
}



/**Functions**/



function objToSession($anObj)
{   
    if(!isset($_SESSION['arObjs']))
    {//initialize session as array
        $_SESSION['arObjs'] = array();
        //append param object to array           
        array_push($_SESSION['arObjs'], $anObj);
    }
    elseif(isset($_SESSION['arObjs']))
    {//loop through each object in array
        foreach($_SESSION['arObjs'] as $obj)
        {
            if($obj->id == $anObj->id)
            {//if object exists, overwrite it
                $obj->id = $anObj->id;
                $obj->name = $anObj->name;
                $obj->url = $anObj->url;
                $obj->timestamp = $anObj->timestamp;
                $obj->refreshTime = $anObj->refreshTime;
                $obj->rawFeed = $anObj->rawFeed;
                $obj->request = $anObj->request;
            }else
            {
                //append param object to array           
                array_push($_SESSION['arObjs'], $anObj);
            }
        }//foreach
    }//elseif

}//function

/**END Functions**/

/**Class**/

class feed{
    public $id;
    public $name;
    public $url;
    public $timestamp;
    public $refreshTime;
    public $rawFeed;
    public $request;    

    function __construct()
    {
        $this->id = 0;
        $this->name = '';
        $this->url = '';
        $this->timestamp = 0;
        $this->rawFeed = '';
        $this->request = '';
        $this->refreshTime = 0 ;   
    }

    
    
    function dbToObj($myID)
    {      
        //take DB info and place into attributes    
        //set sql
        $sql = "SELECT feedName, feedID, feedUrl, feedTimeStamp, feedRawFeed FROM rssFeeds WHERE feedID = " . $myID;

        # connection comes first in mysqli (improved) function
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        if (mysqli_num_rows($result) > 0)
        {#at least one answer!
           while ($row = mysqli_fetch_assoc($result))
           {#set DB values to attributes for each row
                $this->id = (int)$row['feedID']; #process db var
                $this->name = htmlentities($row['feedName']);
                $this->url = dbOut($row['feedUrl']);
                $this->rawFeed = urldecode($row['feedRawFeed']);
               
                //what to do if rawFeed is NULL               
                if($row['feedRawFeed'] != NULL)
                {//decode rawFeed
                   $this->rawFeed = urldecode($row['feedRawFeed']);
                }
                else
                {//make value NULL
                    $this->rawFeed  = NULL;                  
                }//end If
               
               
                //what to do if timestamp is NULL               
                if($row['feedTimeStamp'] != NULL)
                {//force to integer
                    $this->timestamp = (int)$row['feedTimeStamp'];
                    $this->refreshTime = $this->timestamp + 300; //create refresh time
                }
                else
                {
                    $this->timestamp = NULL;                  
                }//end If
            }//while
        }//if        
        mysqli_free_result($result); #free resources
        //generates the request URL
        $this->request = "https://news.google.com/rss/search?q={$this->url}&hl=en-US&gl=US&ceid=US:en";
    }
    
    function test()
    {
        return $this->name;
    }
    
    
    //takes $this->request and stores as raw feed 
    function fetchFresh()
    {
        $this->rawFeed = file_get_contents($this->request);
        $this->timestamp = time(); //create timestamp
        $this->refreshTime = time() + 300; //create refresh time
    }

    
    /*
        //write session rawfeed and timestamp to DB        
        #sprintf() function allows us to filter data by type while inserting DB values.  Illegal data is neutralized, ie: numerics become zero
    */
    function objToDB()
    {
        //SQL insert statement
        $sql = sprintf("UPDATE rssFeeds
        SET feedRawFeed = '%s', feedTimeStamp= %d WHERE feedID = %d", urlencode($this->rawFeed), $this->timestamp, $this->id);
        
        //connect to DB and run SQL command        
        mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));   
    }
    
    
    /*
        *sets DB feedRawFeed = NULL;
        *sets DB feedTimeStamp = NULL ;
        *
        *
        *
    */    
    function clearDbCache()
    {
        //SQL insert statement
        $sql = "UPDATE rssFeeds
        SET feedRawFeed = NULL, feedTimeStamp= NULL WHERE feedID = $this->id";
        
        //connect to DB and run SQL command        
        mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
    }
    
    
    
    /*
    *
    //Takes the $this->id attribute 
    * gets encoded feed from DB, decodes it
    * converts it into an xml object that will bet looped to get the feed output    
    */
    function sessionToView()    
    {
        //go through each object in array
        foreach($_SESSION['arObjs'] as $obj)
        {
            if($obj->id == $this->id)
            {
                //create html view
                $Xml = simplexml_load_string($obj->rawFeed);
                $myArray = array();
                $myArray['result'] = '';
                $myArray['heading'] = "<h3>{$Xml->channel->title}</h3>";    

                $myArray['articleCounter'] = 0; // reset article counter
                $myArray['result'] = '<div class="jumbotron">';
                foreach($Xml->channel->item as $story)
                {
                $myArray['result'] .="
                    <div class=\"card text-white bg-dark mb-3\" style=\"width: 90%;\">
                  <div class=\"card-body\">
                    <p class=\"card-text\">$story->description</p><br />
                    <h6 class=\"card-subtitle text-muted\">$story->source</h6>
                  </div>
                </div><!--END Box Format-->

                ";  

                }//end foreach  
            $myArray['result'] .= '</div><!--Jumbotron-->';
            return $myArray;

            }
        }      

    }//sessionToView
    
    

}//class feed END

/**END Class**/


 # END CONFIG AREA ---------------------------------------------------------- 
get_header(); #defaults to header_inc.php
    
 
//****** helpful feed warnings *******//
//print a summary of new feed action
//echo $myNewWarning;

//print a summary of new feed action
echo $myWarning;
?>

    
<h1><i class="far fa-newspaper"></i>
    <?php 
    //echo heading
    //var_dump($myFeedView['feedState']);    
    echo $myViewResult['feedTitle'] . ' ' . $config->titleTag ;
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

