<?php
//read-feed-simpleXML.php
//our simplest example of consuming an RSS feed


echo '<form action="feed.php" method="post">';

echo '
    <button name="cat" type="submit" value="science">Science</button>
    <button name="cat" type="submit" value="sports">Sports</button>
    <button name="cat" type="submit" value="politics">Polictics</button>
          
	</form>';

if(isset($_POST['cat'])){
  $request = "https://news.google.com/rss/search?q=" . $_POST['cat'];
  $response = file_get_contents($request);
  $xml = simplexml_load_string($response);
  print '<h1>' . $xml->channel->title . '</h1>';
    
  foreach($xml->channel->item as $story)
  {
    echo '<a href="' . $story->link . '">' . $story->title . '</a><br />'; 
    echo '<p>' . $story->description . '</p><br /><br />';
  }

              
}  

?>



