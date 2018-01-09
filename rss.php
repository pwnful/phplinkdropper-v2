<?
/*
script: rss.php
purpose: This script displays the RSS feeds.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

error_reporting(0);
header('Content-type: text/xml'); 

include("config.php");
include("functions.php");

$feed = clean_string($_GET['type']);
$num = clean_string($_GET['limit']);


if (empty($num) || $num < 1)
$num = 10;

if ($feed == "newest_content"){
$query = "SELECT * from items WHERE status = 1 ORDER by date DESC LIMIT $num";
$feedtitle = "Newest Content";
}
elseif ($feed == "most_popular"){
$query = "SELECT * from items WHERE status = 1 ORDER by hits DESC LIMIT $num";
$feedtitle = "Most Popular Content";
}
elseif ($feed == "random_content"){
$query = "SELECT * from items WHERE status = 1 ORDER by RAND() LIMIT $num";
$feedtitle = "Random Content";
}
elseif ($feed > 0){

// Need to make sure its a valid category and get category name
        $sql_query = "SELECT * from categories WHERE status = 1 && id = $feed LIMIT 1";
        $result2 = mysql_query($sql_query);
        if(mysql_num_rows($result2))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result2))
        {
          $categoryname = stripslashes($row['title']);
          
          $query = "SELECT * from items WHERE status = 1 && category = $feed ORDER by date DESC LIMIT $num";
          $feedtitle = "Newest $categoryname";

        }
        }
        else{

          $query = "SELECT * from items WHERE status = 1 && category = $feed ORDER by date DESC LIMIT $num";
          $feedtitle = "Newest Content";
        
        }

// End Category Check


}
else{
$query = "SELECT * from items WHERE status = 1 ORDER by date DESC LIMIT $num";
$feedtitle = "Newest Content";
}

// Now write the header information
$feed = "<?xml version='1.0' ?><rss version='2.0'><channel>";

$feed.="<title>$feedtitle from $sitename</title>";

$feed.="<link>$base_url</link>";

$feed.="<description>$sitedescription</description>";

$feed.="<language>en-us</language>";

$feed.="<docs>".$base_url."rss.php</docs>";



if ($rewrite != 0 && $trackclicks != 0){
    
        $result = mysql_query($query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
                  $mediatitle = ucwords($row['title']);
                  $mediatitle = ereg_replace("[^a-zA-Z0-9 ]", "", $mediatitle);
                  $mediadescription = stripslashes($row['description']);
                  $id = $row['id'];
                  $title = strtolower($mediatitle);
                  // remove all characters except spaces, a-z and 0-9
                  $title = ereg_replace("[^a-z0-9 ]", "", $title);
                  // make all spaces single spaces
                  $title = ereg_replace(" +", " ", $title);
                  // replace spaces with -
                  $title = str_replace(" ", "_", $title);
                  $title = $title.".html";      
    
    	    if ($rewrite ==0)
    	    $itemlink = $base_url."out.php?id=".$id;
    	    else
    	    $itemlink = $base_url."media/".$id."/".$title;
    
                    $feed.="
                    <item>
                    <title>$mediatitle</title>
                    <description>$mediadescription</description>
                    <link>$itemlink</link>
                    </item>
                    ";
                    
    
                
        }
        }
    
}
$feed.="</channel></rss>";
mysql_close($connection);
echo $feed;

?>
