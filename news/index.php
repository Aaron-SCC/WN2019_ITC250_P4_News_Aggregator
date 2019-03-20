<?php
// Show category menu  
# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'New Feed Categories';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'News Aggregator Group Project' . $config->metaDescription;
$config->metaKeywords = 'news, rss, feed'. $config->metaKeywords;

//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

/*
$config->metaDescription = 'Web Database ITC281 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC281,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/
# END CONFIG AREA ---------------------------------------------------------- 
get_header(); #defaults to header_inc.php

?>
<h3 align="center">Feed</h3>

<?php
$catSql = "SELECT categoryID, categoryName FROM rssCategories";

# connection comes first in mysqli (improved) function
$catResult = mysqli_query(IDB::conn(),$catSql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

//custom styles
echo'<style>
.collapsible {
  background-color: #034686;
  color: white;
  cursor: pointer;
  padding: 7px;
	width: 100%;
	border-style: none;
  text-align: left;
  outline: none;
	font-size: .8 em;
	margin: .5px;
	max-width:500px;
}

.active, .collapsible:hover {
  background-color: #d5edfa;
	color: #495069;
}

.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
	background-color: #f1f1f1;
	max-width:500px;
}
</style>';

echo'<div class="jumbotron">';

if(mysqli_num_rows($catResult) > 0)
{#records exist - process
	
	//creates category list
	while($catRow = mysqli_fetch_assoc($catResult))#while loop repeater
	{# process each row
         //echo '<div align="center"><a href="' . VIRTUAL_PATH . 'surveys/survey_view.php?id=' . (int)$row['SurveyID'] . '">' . dbOut($row['Title']) . '</a></div>';
		 //set up sql call for feeds
		 $myId = (int)$catRow['categoryID'];
		 $feedSql = "SELECT feedName, feedID FROM rssFeeds WHERE categoryID=" . $myId;
		 
		 $feedResult = mysqli_query(IDB::conn(),$feedSql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
		 //var_dump($catRow);
		#images in this case are from font awesome
		$prev = '<i class="fa fa-chevron-circle-left"></i>';
		$next = '<i class="fa fa-chevron-circle-right"></i>';
		 
		echo '
			<button type="button" class="collapsible">' . dbOut($catRow['categoryName']) . '
			</button>
			<div class="content">
				<div class="list-group">		
			';


			//This is the feeds list
			while($feedRow = mysqli_fetch_assoc($feedResult))#while loop repeater
				{
					echo '
					<a href="' . VIRTUAL_PATH . '/news/feed_view.php?id=' . (int)$feedRow['feedID'] . '" 
					class="list-group-item list-group-item-action">
						' . dbOut($feedRow['feedName']) . '</li>
					</a>';

					
				}//end while

			echo'
				</div><!--list-group-->
			</div><!--content-->
				';

		@mysqli_free_result($feedResult);
	}//while  loop repeater
	echo'
	<script>
		var coll = document.getElementsByClassName("collapsible");
		var i;

		for (i = 0; i < coll.length; i++) {
		coll[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var content = this.nextElementSibling;
			if (content.style.display === "block") {
			content.style.display = "none";
			} else {
			content.style.display = "block";
			}
		});
		}
	</script>
	';

		
	
}else{#no records
    echo "<div align=center>There are currently no categories</div>";	
}#else
@mysqli_free_result($catResult);

echo '</div> <!--jumbotron-->';
get_footer(); #defaults to footer_inc.php
?>