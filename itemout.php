<?php
/*
script: itemout.php
purpose: This script sends visitors to the link they selected.
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
$report = clean_string($_GET['report']);

// Log user
if ($logusers ==1)
  log_user();
// End Log Users

// If the user reports the content, it will update the database and then
// redirect the user back to the main page
if (!empty($report)){

    $update_items = mysql_query("UPDATE items
    set reports = reports + 1
    where id = '$report'");
    
    // Now check to see if its OVER the REPORTS limit, if so change status to REPORTED
        $pick_item = "SELECT reports FROM items where id = $report LIMIT 1";
		    $result = mysql_query($pick_item);
		    if(mysql_num_rows($result))
		    {
        while($row = mysql_fetch_array($result))
		    {
		    $num_reports = $row['reports'];
		    }
		    }
		    
    if ($num_reports >= $reports && $reports > 0)
      $update_items = mysql_query("UPDATE items
      set status = '2'
      where id = '$report'");    
    
    // End REPORT status change
    
    header('Location: '.$base_url.'index.php');
    exit;


}
// End report

// Time to skim traffic if its enables
// We also have it setup not to skim if a SE spider is spidering your pages
$spider = se_spider();
if(empty($spider)){
    if($skim > 0 && !empty($skimurl)){
    
        $itemhits = $_SESSION['itemhits'];
        $skimhits = $_SESSION['skimhits'];
        $skimratio = ($skimhits / ($itemhits+$skimhits))*100;

        
        if(empty($skimratio) || $skimratio < $skim || !$skimratio || $skimhits < 1){
    
        $skimhits++;
        $_SESSION['skimhits']=$skimhits;
        header('Location: '.$skimurl.'');
        exit; 
        }
        else{
        
        $itemhits++;
        $_SESSION['itemhits']=$itemhits;
        
        }
    
    }
}

// End Traffic Skimming


        $pick_item = "SELECT title,url,userid,votes,points,yes,no FROM items where status = 1 && id = $id LIMIT 1";
		    $result = mysql_query($pick_item);
		    if(mysql_num_rows($result))
		    {
        while($row = mysql_fetch_array($result))
		    {
  
  		    $contenttitle = $row['title'];
  		    $contenttitle = stripslashes($contenttitle);
          $contenturl = $row['url'];
          $submituser = $row['userid'];  		   
  		    $contentvotes = $row['votes'];
  		    $contentpoints = $row['points'];
          $_SESSION['contentyes'] = $row['yes'];
  		    $_SESSION['contentno'] = $row['no'];
  		    
          $_SESSION['contenttitle'] = $contenttitle;
  		    $_SESSION['contentid'] = $id;
  		    
  		    $contentrating = ($contentpoints / $contentvotes);
  		    $_SESSION['contentrating'] = round($contentrating,2);

        }
		    }
		    else{
          // If its an invalid item then the user will be redirected to the main page
          header('Location: index.php');
          exit;
        }

       if(!empty($submituser)){
       // We need to get the user's group and see how many points each hit out is

        $user_group = get_user_group($submituser);
        
        $get_points = "SELECT outpoints FROM groups where status = 1 && id = $user_group LIMIT 1";
		    $result = mysql_query($get_points);
		    if(mysql_num_rows($result))
		    {
        while($row = mysql_fetch_array($result))
		    {
  
            $outpoints = $row['outpoints'];
        
        }
		    }      
       
       
       
       }

      if (($_SESSION['lastitem'] != $id && $trackclicks == 1)){
        
            $update_item = mysql_query("UPDATE items
            set hits = hits + 1
            where id = '$id'");

        // This is setup so if someone RELOADS a page or goes to the same item over and over
        // again it will only count once.
        $_SESSION['lastitem'] = $id;      
      
      }      
      
      $array=parse_url($contenturl);
      $itemdomain1 = $array['host'];
      $itemdomain2 = str_replace("www.", "", $itemdomain1);
      $itemdomain3 = "http://".trim($itemdomain2);
      $itemdomain4 = "http://www.".$itemdomain2;
      
      if (!empty($itemdomain1)){
      
      
      // Use the follwing if you want to count every hit out on items against a user's account even if the user
      // did not submit the item
      
      /*
      $add_hit = mysql_query("update domains
                     set hitsout=hitsout+1
                     where domain like '$itemdomain1%' || domain like '$itemdomain3%' || domain like '$itemdomain4%' LIMIT 1");
      $update_user = mysql_query("update users
                     set points=points-$outpoints
                     where id = '$submituser'");
                     
      */             

      // Use the follwing if you want to count every hit out on items against a user's account only if the user themself
      // submitted the item

      if($uniqueinout == 1){

        $visiteddomains = $_COOKIE['visiteddomains'];

        if(strpos($visiteddomains,$itemdomain1)===false && strpos($visiteddomains,$itemdomain2)===false && !empty($itemdomain1)){
        $loghitout = "yes";      
        
          $midnight = strtotime("tomorrow 00:00");
          $now = time();
          $expirecookie = ($midnight-$now);
          
          $visiteddomains.= " | ".$itemdomain1;
          
          setcookie("visiteddomains", "$visiteddomains", time()+$expirecookie);              
        
        }
      
      }else{
        $loghitout = "yes";
      }
        if($loghitout == "yes"){
  
          $add_hit = mysql_query("update domains
                         set hitsout=hitsout+1,
                         dayout=dayout+1
                         where domain like '$itemdomain1%' || domain like '$itemdomain2%' || domain like '$itemdomain3%' || domain like '$itemdomain4%' && userid ='$submituser' LIMIT 1");
          $update_user = mysql_query("update users
                         set points=points-$outpoints
                         where id = '$submituser'");
  
        }
            


      }

  // If the user has turned off FRAME OUT, then we will just direct to the URL itself
  if ($frameout == 0){
  
    header('Location: '.$contenturl.'');
    exit; 
    
  }



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="description" content="<?=$contenttitle;?>">
<title><?=$sitename;?> :: <?=$contenttitle;?></title>
</head>

<frameset rows="100,*" frameborder="no" border="0" framespacing="0">
  <frame src="<?=$base_url;?>/templates/<? echo $template_directory;?>/topframe.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame">
  <frame src="<?=$contenturl;?>" name="mainFrame" id="mainFrame" title="mainFrame">
</frameset>
<noframes><body>
</body>
</noframes></html>
