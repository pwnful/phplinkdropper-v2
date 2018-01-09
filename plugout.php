<?php

/*
script: plugout.php
purpose: This script sends visitors to plug URLs once they click on the PLUG LINK.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

session_start();
error_reporting(0);

include ("config.php");
include ("functions.php");

$id = clean_string($_GET['id']);


        $pick_plug = "SELECT * FROM plugs where status = 1 && id = $id LIMIT 1";
		    $result = mysql_query($pick_plug);
		    if(mysql_num_rows($result))
		    {
        while($row = mysql_fetch_array($result))
		    {
  
  		    $plugurl = stripslashes($row['url']);
  		    $plugmaxclicks = $row['maxclicks'];
  		    $plugclicks = $row['clicks'];
  		    $plugclicks++;

  		    $plugmaximpressions = $row['maximp'];
  		    $plugimpressions = $row['impressions'];
  		    $plugimpressions++;
  		    
  		    $update_clicks_and_impressions = mysql_query("UPDATE plugs
          set clicks=clicks+1,
          impressions=impressions+1
          where id = '$id'");
          
          // If the plug had a max hit amount and its been met, time it to inactive
          if(($plugmaxclicks>0 && $plugclicks>=$plugmaxclicks) || ($plugmaximpressions>0 && $plugimpressions>=$plugmaximpressions)){
          
          $update_plug_status = mysql_query("UPDATE plugs
          set status = '0'
          where id = '$id'");
          
          
          }
          

        }
		    }
		    else{
          // If its an invalid item then the user will be redirected to the main page
          header('Location: index.php');
          exit;
        }

 
    header('Location: '.$plugurl.'');
    exit; 



?>
