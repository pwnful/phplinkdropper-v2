<?php

/*
script: linkout.php
purpose: This script sends visitors to LINK's URL once they click on the link. This also records the stats.
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


        $pick_link = "SELECT url FROM links where status = 1 && id = $id LIMIT 1";
		    $result = mysql_query($pick_link);
		    if(mysql_num_rows($result))
		    {
        while($row = mysql_fetch_array($result))
		    {
  
  		    $linkurl = stripslashes($row['url']);

          if($uniqueinout == 1){

            $array=parse_url($linkurl);
            $linkdomain1 = $array['host'];
            $linkdomain2 = str_replace("www.", "", $linkdomain1);
            $linkdomain3 = "http://".trim($linkdomain2);
            $linkdomain4 = "http://www.".$linkdomain2;

    
            $visiteddomains = $_COOKIE['visiteddomains'];
    
            if(strpos($visiteddomains,$linkdomain1)===false && strpos($visiteddomains,$linkdomain2)===false && !empty($linkdomain1)){
            $loglinkhit = "yes";      
            
              $midnight = strtotime("tomorrow 00:00");
              $now = time();
              $expirecookie = ($midnight-$now);
              
              $visteddomains.= " | ".$linkdomain1;
              
              setcookie("visiteddomains", "$visiteddomains", time()+$expirecookie);              
            
            }
          
          }else{
            $loglinkhit = "yes";
          }




            if($loglinkhit=="yes"){
    		    
    		    $update_clicks = mysql_query("UPDATE links
            set dayout=dayout+1,
            totalout=totalout+1
            where id = '$id'");
            
            }

        }
		    }
		    else{
          // If its an invalid item then the user will be redirected to the main page
          header('Location: index.php');
          exit;
        }

 
    header('Location: '.$linkurl.'');
    exit; 



?>
