<?php

class feed{
    public $id;
    public $name;
    public $url;
    public $timestamp;
    public $rawFeed;

    function __construct($myID)
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
                $this->rawFeed = $row['feedRawFeed'];
               
                //what to do if timestamp is NULL
                if(isset($row['feedTimeStamp']))
                {//timestamp is null
                    $this->timestamp = (int)$row['feedTimeStamp'];
                }
                else
                {
                    $this->timestamp = NULL;                  
                }//end If
            }//while
        }//if
        
        mysqli_free_result($result); #free resources
    }
    
    function test()
    {
        return $this->name;
    }
    

    /*
    * toSession()
    * Takes the $this->url attribute and passes raw feed and timestamp tp SESSION Vars
    * Saves encoded Raw feed into DB for corresponding record
    * 
    */
    
    function newToSession()
    {   
        //get feed in raw form. 
        $_SESSION['request'] = "https://news.google.com/rss/search?q={$this->url}&hl=en-US&gl=US&ceid=US:en";       
        $_SESSION['rawFeed'] = file_get_contents($_SESSION['request']);

        $_SESSION['timestamp'] = time(); // create timestamp
        $_SESSION['refreshTime'] = $_SESSION['timestamp'] + 30; //create refresh time
        //var_dump($_SESSION['request']);   
    }
    
    
    /*
        //write session rawfeed and timestamp to DB        
        #sprintf() function allows us to filter data by type while inserting DB values.  Illegal data is neutralized, ie: numerics become zero
    */
    function sessionToDB()
    {
        //SQL insert statement
        $sql = sprintf("UPDATE rssFeeds
        SET feedRawFeed = '%s', feedTimeStamp= %d WHERE feedID = %d", urlencode($_SESSION['rawFeed']), $_SESSION['timestamp'], $this->id);
        
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
        $Xml = simplexml_load_string($_SESSION['rawFeed']);
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

            $myArray['articleCounter'] ++; //count article iterations

        }//end foreach  
        $myArray['result'] .= '</div><!--Jumbotron-->';
        return $myArray; 
    }//sessionToView
    
    

}//class feed END
?>