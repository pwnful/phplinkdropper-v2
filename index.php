<?php
session_start();
//error_reporting(0);

include ("config.php");
include ("functions.php");


if(file_exists("./templates/$template_directory/template_functions.php"))
include ("./templates/$template_directory/template_functions.php");


// Nightly Reset
        $reset_query = "select lastreset from siteconfig";
        $reset_result = mysql_query($reset_query);
        if(mysql_num_rows($reset_result))
		    {
		    while($row = mysql_fetch_array($reset_result))
		    {
        $lastreset = $row['lastreset'];
        
        if($lastreset < strtotime("-1 day"))
        @include("./admin/nightly.php");
        
        }
        }

// Log user
if ($logusers ==1)
  log_user();
// End Log Users



// See if this visitor was referred by a link on your list
if($uniqueinout==1){

      $referral = $_SERVER['HTTP_REFERER'];
      $array=parse_url($referral);
      $referral1 = $array['host'];
      $referral2 = str_replace("www.", "", $referral1);
      $referral3 = "http://".trim($referral2);
      $referral4 = "http://www.".$referral2;
      // If referrer is in database, then add a hit!


      if(!empty($_COOKIE["refdomains"]) && !empty($referral1)){
      
      $refdomains = $_COOKIE["refdomains"];
        if(strpos($refdomains,$referral1)===false && strpos($refdomains,$referral2)===false && !empty($referral1))
        $loghit = "yes";
      }else{
        $loghit = "yes";
      }


      if (!empty($referral1) && $loghit == "yes"){
      $add_hit = mysql_query("update links
                     set totalin=totalin+1,
                     dayin=dayin+1 
                     where url like '$referral1%' || url like '$referral3%' || url like '$referral4%' LIMIT 1");

      // Update the user & domain stats if it was a user domain who the hit came from
      credit_user_domain($referral1,$referral2);
      // End user and domain update

      $_SESSION['refdomain'] = $referral2;
      $_SESSION['refpage'] = $_SERVER['HTTP_REFERER'];

      $midnight = strtotime("tomorrow 00:00");
      $now = time();
      $expirecookie = ($midnight-$now);
      
      $refdomains.= " | ".$referral1;
      
      setcookie("refdomains", "$refdomains", time()+$expirecookie);      
      
      
      }



}
elseif($rawinout==1){

      $referral = $_SERVER['HTTP_REFERER'];
      $array=parse_url($referral);
      $referral1 = $array['host'];
      $referral2 = str_replace("www.", "", $referral1);
      $referral3 = "http://".trim($referral2);
      $referral4 = "http://www.".$referral2;

      // If referrer is in database, then add a hit!
      if (!empty($referral1)){
      $add_hit = mysql_query("update links
                     set totalin=totalin+1,
                     dayin=dayin+1 
                     where url like '$referral1%' || url like '$referral3%' || url like '$referral4%' LIMIT 1");

      // Update the user & domain stats if it was a user domain who the hit came from
      credit_user_domain($referral1,$referral2);
      // End user and domain update

      $_SESSION['refdomain'] = $referral2;
      $_SESSION['refpage'] = $_SERVER['HTTP_REFERER'];

      }

}
else{
  if(!isset($_SESSION['referral'])) { 
      $referral = $_SERVER['HTTP_REFERER'];
      $array=parse_url($referral);
      $referral1 = $array['host'];
      $referral2 = str_replace("www.", "", $referral1);
      $referral3 = "http://".trim($referral2);
      $referral4 = "http://www.".$referral2;
      // If referrer is in database, then add a hit!
      if (!empty($referral1)){
      $add_hit = mysql_query("update links
                     set totalin=totalin+1,
                     dayin=dayin+1 
                     where url like '$referral1%' || url like '$referral3%' || url like '$referral4%' LIMIT 1");

      // Update the user & domain stats if it was a user domain who the hit came from
      credit_user_domain($referral1,$referral2);
      // End user and domain update

      }
      $_SESSION['referral'] = 1;
      $_SESSION['refdomain'] = $referral2;
      $_SESSION['refpage'] = $_SERVER['HTTP_REFERER'];
  } 
}

// End link check

// See if the user was referred by another user 

  if(empty($_SESSION['ureferral']) && isset($_GET['ref'])) { 
      $refid = clean_string($_GET['ref']);
      // If they were referred, save it into a session. If the user registers
      // then we will have the referring users id.
      if ($refid > 0)
      $_SESSION['ureferral'] = $refid;
      
  } 

// End user Referral Check


if ($sitestatus == 0){
echo $offlinemessage;
exit;
}

// Time to check if IP is banned
if (ban_check("site") == "banned"){

  echo nl2br(stripslashes($bannedmessage));
  exit;
}
// End BAN Check

// Now we are checking to see if their are any items in the publishing queue
// that haven't been published yet since the last check
// We put this in a session varibale to cut down on SQL Queries
// CODE UPDATED: 11/8/2007
if(empty($_SESSION['lastupdatecheck'])){
      $lastupdatecheck = time();
      $now=time();

      $sql = "SELECT max(date) AS max_publish_date from items WHERE status = '1' && publishdate > 0 LIMIT 1";
      //store the SQL query in the result variable
      $result = @mysql_query($sql);
      $i = mysql_fetch_array($result); 
      $lastupdate = $i['max_publish_date']; 

    
    if($lastupdatecheck > ($lastupdate+$publishtime)){

      $publish_items = mysql_query("UPDATE items
      set date = '$now',
      status = '1'
      where status = '99' && publishdate < $now ORDER BY date ASC LIMIT $max_publish");

      // Removes the publish date from any items that were just published
      // (or really any items that is active with a publish date since they are active)
      // so that the script knows next time what the maximum publish date was
      // and can determine the time between items being published.
      $update_published_items = mysql_query("UPDATE items
      set publishdate = ''
      where status = '1' && publishdate < ($now-86400)");
    }
    
    $_SESSION['lastupdatecheck'] = $lastupdatecheck;
}
// End publishing

if($_GET['ac']=="pv" && !empty($_GET['id']) && !empty($_GET['s']))
$localaction="playvideo";

$action = clean_string($_GET['action']);
$c = clean_string($_GET['c']);
$page = clean_string($_GET['page']);
$limit = clean_string($_GET['limit']);


// Check to see if user has a cookie, and if they aren't logged in, log them in
if (isset($_COOKIE["un"]) && isset($_COOKIE["unp"]) && $_SESSION['loggedin'] != 1){
$frompage = curPageURL();
$username = clean_string($_COOKIE["un"]);
$password = clean_string($_COOKIE["unp"]);
login_user($base_url,$username,$password,$frompage,1,$rewrite);
}
// End Cookie Logging In

// Check to see if the user is submitting an item and if so send them to the submission place
if(isset($_POST['contenttitle']) || isset($_POST['contenturl']))
$action="submit";
// End submit item

// Check to see if user filled out login form and if so, send them to the login
if (isset($_POST['loginuser']) && isset($_POST['loginpassword'])){
$frompage = $_SERVER['PHP_SELF'];
$username = clean_string($_POST['loginuser']);
$password = md5(clean_string($_POST['loginpassword']));
$saveme = clean_string($_POST['saveme']);

login_user($base_url,$username,$password,$frompage,$saveme,$rewrite);
}

// End Login Form Check

// Check to see if the user is searching, and if so send them to that area
if(!empty($_POST['t']))
$action="search";
// End Search Check

// Check to see if the user filled out the add domain form and if so send them to that area
if (isset($_POST['domain']) || isset($_POST['domaintitle']))
$action = "adddomain";

// Check to see if the user filled out the add domain form and if so send them to that area
if (isset($_POST['cid']))
$action = "addcomment";

if (isset($_POST['forgotpwemail'])){
$emailaddress = clean_string($_POST['forgotpwemail']);
send_password($emailaddress,$sitename,$contact_email,$rewrite,$base_url);
exit;
}

// Check to see if the user filled out the add domain form and if so send them to that area
if (isset($_POST['newpw1']) || isset($_POST['newpw2']))
$action = "updatepw";

if ($action == "submit" && ($member_submit == 0 || ($member_submit==1 && $_SESSION['loggedin']==1))){

        $frompage = $_SERVER['HTTP_REFERER'];
        $userid = $_SESSION['loggedinuserid'];

                
        if($_POST['thumburl']=="http://")
        $_POST['thumburl']="";

        // Check to see if the user is trying to bypass your requirements, and if so, redirect them!
        if ($_SESSION['nosubmit']==1){
          $_SESSION['submitstatus'] = "Cannot Add Submission At This Time";
            header('Location: '.$frompage.'');
            exit;        
        
        }
        // End Cheat Check
        
        // Check to see if IP address is allowed to submit. If not, redirect!
        if (ban_check("submit") == "banned"){
          $_SESSION['submitstatus'] = "Cannot Add Submission At This Time";
            header('Location: '.$frompage.'');
            exit;        
        
        }
        // End Ban Check
        
        $submissiontime = time();
        if (($submissiontime - $delay) <= $_SESSION['submission']){
         $_SESSION['submitstatus'] = "Flood Control Initiated";
            header('Location: '.$frompage.'');
            exit; 
        }
        
        $ipaddress = $_SERVER['REMOTE_ADDR']; 
        $contenttitle = clean_string($_POST['contenttitle']);
        $contentdescription = clean_string($_POST['contentdescription']);
        $contenturl = clean_string($_POST['contenturl']);
        $contenturl2 = strtolower($contenturl);
        $category = clean_string($_POST['category']);
                
          // Make sure they selected a category
         if ($category == 0){
         $_SESSION['submitstatus'] = "Please select a category";
            header('Location: '.$frompage.'');
            exit; 
        }


        // Check if TITLE and URL are filled in
        if (empty($contenttitle) || $contenttitle == "Title?"){
         $_SESSION['submitstatus'] = "Please Fill In Title";
            header('Location: '.$frompage.'');
            exit; 
        }
        
        elseif (empty($contenturl) || $contenttitle == "http://"){
         $_SESSION['submitstatus'] = "Invalid URL";
            header('Location: '.$frompage.'');
            exit; 
        }

               

        // Check if VALID URL
        if (is_url("$contenturl")) {     
        } else { 
            $_SESSION['submitstatus'] = "Doesn't seem to be a valid URL";
            header('Location: '.$frompage.'');
            exit; 
        } 

        // Visit URL to see if its up and running
        
        $ch = @curl_init();
        
        if($ch !== false){
          curl_setopt($ch, CURLOPT_URL, $contenturl);
          curl_setopt($ch, CURLOPT_HEADER, true);
          curl_setopt($ch, CURLOPT_NOBODY, true);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //follow up to 10 redirections - avoids loops
          $data = curl_exec($ch);
          curl_close($ch);
          preg_match_all("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
          $code = end($matches[1]);
          if(!$data) {
           
                       $_SESSION['submitstatus'] = "Doesn't seem to be a valid URL"; 
                       header('Location: '.$frompage.''); 
                       exit;   
           
           
          }
        }
               
        // End Visting URL

        // Check to see if they submitted a thumbnail if it is required
        if($require_thumb == 1 && empty($_POST['thumburl']) && empty($_FILES['userfile']['tmp_name'])){

            $_SESSION['submitstatus'] = "A thumbnail is required for a valid submission.";
            header('Location: '.$frompage.'');
            exit;         
        
        }

        // Check if already submitted
              $query = "select id from items where (url = '$contenturl' || url = '$contenturl2')";
              $result = mysql_query($query);
                if (mysql_num_rows($result)>0)
                {
                $_SESSION['submitstatus'] = "That link is already in our database.";
                header('Location: '.$frompage.'');
                exit;
                }
        
        // Check to see if the URL is banned.
             $array=parse_url($contenturl2);
              $domaincheck = $array['host'];
              $domaincheck1 = str_replace("www.", "", $domaincheck);
              $domaincheck2 = "http://".trim($domaincheck1);
              $domaincheck3 = "http://www.".$domaincheck2;


              $query = "select id from banned where (url = '$contenturl' || url = '$contenturl2' || url = '$domaincheck' || url = '$domaincheck1' || url = '$domaincheck2' || url = '$domaincheck3') && url != ''";
              $result = mysql_query($query);
                if (mysql_num_rows($result)>0)
                {
                $_SESSION['submitstatus'] = "Cannot Approve Submission";
                header('Location: '.$frompage.'');
                exit;
                }


        // Check if over max per 24 hour period for the domain
                
              // get host name from URL
              preg_match("/^(http:\/\/)?([^\/]+)/i",
                  "$contenturl2", $matches);
              $host = $matches[2];
              
              // get last two segments of host name
              preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
              $url = $matches[0];
              $now = time();
              $timelimit = ($now - 86400);
             
              if ($maxpersite > 0 && $_SESSION['maxday']< 1){
                  $query2 = "select id from items where date > $timelimit && url LIKE '%$url%'";
                  $result2 = mysql_query($query2);
                    if (mysql_num_rows($result2)>$maxpersite)
                    {
                      
                      $_SESSION['submitstatus'] = "24 Hour Submission Limit Reached for URL";
                      header('Location: '.$frompage.'');
                      exit;
                    }
              }

              if ($_SESSION['maxday'] > 0){
                  $userid = $_SESSION['loggedinuserid'];
                  $query2 = "select id from items where date > $timelimit && userid = '$userid'";
                  $result2 = mysql_query($query2);
                    if (mysql_num_rows($result2)>$_SESSION['maxday'])
                    {
                      
                      $_SESSION['submitstatus'] = "24 Hour Submission Limit Reached for your account";
                      header('Location: '.$frompage.'');
                      exit;
                    }
              }

              if($_SESSION['loggedin']==1){
              
                  $query2 = "select id from domains where domain='$url' && userid='$userid'";
                  $result2 = mysql_query($query2);
                    if (mysql_num_rows($result2)<1)
                    {
                      
                      $_SESSION['submitstatus'] = "$url is not a valid domain in your account. Submission cannot be made.";
                      header('Location: '.$frompage.'');
                      exit;
                    }              
              
              
              
              }

          
          if(!empty($_POST['thumburl']) || !empty($_FILES['userfile']['tmp_name'])){
          
            $thumburl = clean_string($_POST['thumburl']);
            
            // Checks to see if the thumbnail is JPG and if not rejects it
            if(strpos($thumburl,".jpg")===false && !empty($humburl)){
            
                      $_SESSION['submitstatus'] = "Thumbnail must be in JPG format to be accepted.";
                      header('Location: '.$frompage.'');
                      exit;            
            
            }
            else{


                          	  	$alphanum = "APBHCPDEFGHIJKLILNKMDRNOPPERQRSTUVWXYZ123456789";
                          			// generate a random 3 character code to play in front of a duplicate file name that
                          			// has been transferred.
                          			$rand = substr(str_shuffle($alphanum), 0, 3);

                                  $imagedirectory = "./content/images/"; 

                                  if ($auto_chmod ==1)
                                  chmod("$imagedirectory",0777);


                                	  if (!empty($_FILES['userfile']['tmp_name'])) {
                                	  
                                    	  $uploadFile = $imagedirectory . $_FILES['userfile']['name'];
                                        
                                        $name2 =$_FILES['userfile']['name'];
                                        
                                        $ext = ereg_replace("^.+\\.([^.]+)$", "\\1", $name2);
                              	   	  	if($ext != "jpg") 
                              	   	  	{
                                            $_SESSION['submitstatus'] = "Thumbnail must be in JPG format to be accepted. $ext";
                                            header('Location: '.$frompage.'');
                                            exit;    
                              	   	  	}
                  
                              					// Make sure that filename isn't already in the directory ... if it is, rename it
                              					if (file_exists($uploadFile))
                              					{
                              					$name2 = $rand.'_'.$name2;
                              					$uploadFile = $imagedirectory . $name2;
                              					}
                              					// End the renaming
                              
                              	   
                                    	 move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile);
                                    	  
                                    	  }
 
                                  else{

                                		    // Start -- Get the file name. (Dont Edit) 
                                        $name2 = $thumburl; 
                                        while(strstr($name2,"/")){ 
                                          $name2 = substr($name2,strpos($name2,"/")+1,999); 
                                        } 
                                        $newname2 = $imagedirectory.$name2; 
                                        // End -- Get the file name. (Dont Edit) 
                                        
                          			       // Make sure that filename isn't already in the directory ... if it is, rename it
                    		
                                    			if (file_exists($newname2))
                                    			{
                                    			$name2 = $rand.'_'.$name2;
                                                $newname2 = $imagedirectory.$name2; 
                                    			}
                                    			// End the renaming
    
                                      $ch = curl_init ($thumburl);
                                      $fp = fopen ($newname2, "w");
                                      curl_setopt ($ch, CURLOPT_FILE, $fp);
                                      curl_setopt ($ch, CURLOPT_HEADER, 0);
                                      curl_exec ($ch);
                                      curl_close ($ch);
                                      fclose ($fp);             
                
                                      if(filesize($newname2)<10){
                                      
                                          $_SESSION['submitstatus'] = "Unable to grab thumbnail. Please try a different file.";
                                          header('Location: '.$frompage.'');
                                          exit;                                      
                                      
                                      }
                                }  
                                              
          		// Now we need to re-size the image if its larger than its supposed to be
          		
              $size = getimagesize($imagedirectory.$name2);
              
              $height = $size[1];
              $width = $size[0];
              
              if ($max_thumb_height > 0 && $height > $max_thumb_height)
               {
                     $thumbheight = $max_thumb_height;
                     $percent = ($height / $thumbheight);
                     $thumbwidth = ($width / $percent);
                     if ($max_thumb_width > 0 && $thumbwidth > $max_thumb_width)
                     {
                         $thumbwidthold = $thumbwidth;
                         $thumbwidth = $max_thumb_width;
                         $percent = ($thumbwidthold / $thumbwidth);
                         $thumbheight = ($thumbheight / $percent);
                     }
                     
                     
               }
              elseif ($max_thumb_width > 0 && $width > $max_thumb_width)
               {
                     $thumbwidth = $max_thumb_width;
                     $percent = ($width / $thumbwidth);
                     $thumbheight = ($height / $percent);
                     if ($max_thumb_height > 0 && $thumbheight > $max_thumb_height)
                     {
                         $thumbheightold = $thumbheight;
                         $thumbheight = $max_thumb_height;
                         $percent = ($thumbheightold / $thumbheight);
                         $thumbwidth = ($thumbwidth / $percent);
                     }
               
               
               }          		
          		 
          		 if(!empty($thumbheight) && !empty($thumbwidth))
          		 resize($imagedirectory . $name2,$width,$height,$thumbwidth,$thumbheight);
          		
          		
          		// End resizing!
              
              
              
              
              
              
              if ($auto_chmod ==1)
              chmod("$imagedirectory",0755);            
            
            }
          
          
          
          }
          
          
          
          // End Valid Image Check



          if($_SESSION['preapproved']==1 || $_SESSION['approval']==1)
          $submitstatus = 1;
          elseif($_SESSION['approval']==99)
          $submitstatus = 99;
          elseif (($submissions == 0 && empty($submitstatus)) || $_SESSION['approval']==3)
          $submitstatus = 3;
          else
          $submitstatus = 1;

         

          $userid = $_SESSION['loggedinuserid'];

          // Insert into Database
          $insert_content = mysql_query("INSERT into items
          set title = '$contenttitle',
          description = '$contentdescription',
          date = '$now',
          url = '$contenturl',
          category = '$category',
          image = '$name2',
          ipaddress = '$ipaddress',
          userid = '$userid',
          status = '$submitstatus'");
          
          $_SESSION['submission'] = $now;
          
                if ($submissions == 1)
                $_SESSION['submitstatus'] = "Link Added! Thank You!";
                else
                $_SESSION['submitstatus'] = "Submission received. Will be posted once confirmed!";
                header('Location: '.$frompage.'');
                exit;



}

elseif ($action == "submititem" && ($member_submit == 0 || ($member_submit==1 && $_SESSION['loggedin']==1))){

// Get some user stats
    if($_SESSION['loggedin']==1){
    
    $loggedinuserid = $_SESSION['loggedinuserid'];
    
        $sql_query = "SELECT points FROM users WHERE id = '$loggedinuserid' LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $userpoints = $row['points'];
        }
        }

        $sql = "SELECT SUM(hitsin)AS sum_hitsin, SUM(hitsout)AS sum_hitsout FROM `domains` WHERE userid = '$loggedinuserid' && status = 1"; 
        $result = mysql_query($sql);
        $i = mysql_fetch_array($result);
        $hitsout = $i['sum_hitsout']; 
        $hitsin = $i['sum_hitsin'];

        if(empty($hitsout))
        $hitsout = 0;
        if(empty($hitsin))
        $hitsin = 0;

        if($hitsout > 0)
        $hitsratio = ($hitsin / $hitsout)*100;
        else
        $hitsratio = $hitsin*100;
        
        $hitsratio = round($hitsratio,2);
        
                
        if($hitsratio < $_SESSION['ratio'] && $_SESSION['ratio'] > 0){
        $hitsratio = "<font color=\"red\">$hitsratio</font>";
        $nosubmit = 1;
        }
        
        if($hitsin < $_SESSION['minhits'] && $_SESSION['minhits'] > 0){
        $hitsin = "<font color=\"red\">$hitsin</font>";
        $nosubmit = 1;
        }
        
        if($userpoints < $_SESSION['minpoints'] && $_SESSION['minpoints'] > 0){
        $userpoints = "<font color=\"red\">$userpoints</font>";             
        $nosubmit = 1;
        }

        if($_SESSION['preapproved']!=1)
        $_SESSION['nosubmit'] = $nosubmit;
        else
        $_SESSION['nosubmit'] = 0;
    
    }
// End  User Stats



    $includefile = "./templates/$template_directory/submitform.html";
    $pagetitle = "Submit Item to $sitename";

}

elseif($action == "search"){

  $searchid = clean_string($_GET['id']);
  $t = clean_string($_POST['t']);
  $now = time();
  
   // Check to see if there are any characters left to search. If not, redirect the user
  if ((isset($_POST['t'])) && (strlen(make_friendly_no_ext($t)) < 1)){
  
  $goback = $_SERVER['HTTP_REFERER'];
  $_SESSION['searchstatus'] = "Not A Valid Search";
  header("Location: $goback"); 
  exit;
  
  }
  
  if (!empty($t)){
  // Insert search into search history
  // If its already been searched for, then increase the number of searches by one
      $sql = "SELECT count(id) AS already_searched from searches WHERE searchterm = '$t'";
      //store the SQL query in the result variable
      $result = @mysql_query($sql);
      $i = mysql_fetch_array($result); 
      $already_searched = $i['already_searched']; 
      
            
      if ($already_searched > 0)
      $update_search = mysql_query("UPDATE searches
      set amount = amount + 1,
      date = '$now'
      where searchterm = '$t'");
      
      else
      $insert_search = mysql_query("INSERT into searches
      set amount = '1',
      date = '$now',
      searchterm = '$t',
      results = '$numrows'");
      

      // Now just get the search ID
      $sql_query = "SELECT id FROM searches WHERE searchterm = '$t'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $searchid = $row['id'];
        }
        }
  //
  }
  
  // Update the search count if someone visits search directly from URL
  if ($searchid > 0 && empty($t)){
  
        $sql_query = "SELECT searchterm FROM searches WHERE id = '$searchid'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $t = stripslashes($row['searchterm']);
        }
        }
          
  
      $update_search = mysql_query("UPDATE searches
      set amount = amount + 1,
      date = '$now'
      where id = '$searchid'");
      
      
  }
    $searchlink = make_friendly($t);
    $includefile = "./templates/$template_directory/main.php";
    $pagetitle = "Search Results for $t";

}

elseif ($action=="rss"){

    $includefile = "./templates/$template_directory/rss.html";
    $pagetitle = "RSS Feed Listing for $sitename";

}

elseif ($action =="tradelinks"){

    $includefile = "./templates/$template_directory/tradelinks.html";
    $pagetitle = "Exchange links with $sitename";

}

elseif ($action =="linkadded"){

    $websitename = clean_string($_POST['websitename']);   
    $websiteurl = clean_string($_POST['websiteurl']);   
    $websitedescription = clean_string($_POST['websitedescription']);   
    $emailaddress = clean_string($_POST['emailaddress']);
    
    add_link($websitename,$websiteurl,$websitedescription,$emailaddress,$base_url);
            
    $includefile = "./templates/$template_directory/linkadded.html";
    $pagetitle = "Your link has been added!";

}

elseif ($action=="links"){

    $includefile = "./templates/$template_directory/links.html";
    $pagetitle = "$sitename Links Page";

}

elseif ($action=="register"){

    if ($_SESSION['loggedin']!=1 && $member_login==1)	{
        $includefile = "./templates/$template_directory/register.html";
        $pagetitle = "$sitename Registration Page";
        }
    else{
    
            header("Location: ".$base_url."index.php"); 
            exit;
    
    
      }    
        
}


elseif ($action == "newmember" && $_SESSION['loggedin']!=1 && $member_login==1)
{        

        $newusername = strip_tags($_POST['newusername']);
        $newusername = clean_string($newusername);
        
        // The following will only allow letters and numbers in a user name
        if (!preg_match("#^([a-zA-Z0-9_]+)$#", $newusername)) {
        
        $_SESSION['status'] = "Invalid characters in username!";
        header("Location: ".$base_url."index.php?action=register"); 
        exit;
        } 
       // End valid check
       
        $newpasswordone = clean_string($_POST['newpasswordone']);
        $newpasswordtwo = clean_string($_POST['newpasswordtwo']);
        $newemailaddress = strip_tags($_POST['newemailaddress']);
        $newemailaddress = clean_string($newemailaddress);
       
       /* Un-comment if you wish to have the script check is an email address is valid!
       //Check to see if email address is valid!
        $email_domain = explode("@",$newemailaddress);
        if (!checkdnsrr($email_domain[1],"MX")){
        $_SESSION['status'] = "Invalid Email Address";
        header("Location: ".$base_url."index.php?action=register"); 
        exit;
        }
      */
      
          // Checks to see the email address is already in the database
          $query2 = "select id from users where email = '$newemailaddress'";
          $result2 = mysql_query($query2);
          if (mysql_num_rows($result2)>0)
            {
            $_SESSION['status'] = "Only one account per email address!";
            header("Location: ".$base_url."index.php?action=register"); 
            exit;
            }
        
        
        if ($newpasswordone != $newpasswordtwo)
        {
          $_SESSION['status'] = "Your passwords did not match.";
            header("Location: ".$base_url."index.php?action=register"); 
            exit;
        }
                
        // Checks to make sure all info is entered
        elseif (!$newusername || !$newpasswordone || !$newpasswordtwo || !$newemailaddress)
        {
          $_SESSION['status'] = "You did not enter all the required information.";
          header("Location: ".$base_url."index.php?action=register"); 
          exit;
        }
        
        else
        {
          // Checks to see if the user is already in the database
          $query2 = "select id from users where username = '$newusername'";
          $result2 = mysql_query($query2);
          if (mysql_num_rows($result2)>0)
            {
            $_SESSION['status'] = "I'm sorry, that username is already taken.";
            header("Location: ".$base_url."index.php?action=register"); 
            exit;
            }
          
          else
            {
            
              if ($email_confirmation == 1)
              {
              //Generate unique confirmation code
              $random= ""; 
              srand((double)microtime()*1000000); 

              $block = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
              $block .= "0123456789"; 
  
               for($i = 0; $i < 10; $i++) 
               { 
              $random .= substr($block,(rand()%(strlen($block))), 1); 
                } 
            
            if($rewrite == 1)
            $confirmlink = $base_url."confirm-account/$random/".$newusername.".html";
            else
            $confirmlink = $base_url."index.php?action=confirmaccount&username=$newusername&confirmation=$random";
            
             //End confirmation code generation
            $msg .=''.$newusername.':<br />';
            $msg .='Thank you for registering with <a href="'.$base_url.'" target="_blank"><strong>'.$sitename.'</strong></a>.<br /><br />';
            $msg .='Please click on the link below to confirm your account with us:<br />';
            $msg .='<a href='.$confirmlink.'>Click here to Confirm</a><br /><br />';
            $msg .='If you cannot see the hyperlink or have problems confirming your account, please copy and paste the following link into your browsers address bar:<br />';
            $msg .=''.$confirmlink.'<br /><br />';
            $msg .='Thank you!.';

            $to = "$newemailaddress";
            $subject = "Please confirm your account with ".$sitename."";
            $headers ="Return-Path: ".$contact_email."\r\n";
            $headers .= "From: ".$sitename." <".$contact_email.">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html\r\n"; 
            mail($to, $subject, $msg, $headers);
            $userstatus = "3";
            }
            else
            {
            $userstatus = "1";

                  // If the user was referred then give them their points
                                   
                  if($_SESSION['ureferral']>0){
                      $referredby = $_SESSION['ureferral'];
                      
                      $sql_query = "SELECT group FROM users WHERE id = '$referredby'";
                      $result = mysql_query($sql_query);
                      if(mysql_num_rows($result))
                      {
                      //output as long as there are still available fields
                      while($row = mysql_fetch_array($result))
                      {
                      $group = $row['ugroup'];
                      }
                      }
                      else{
                      $group = 0;
                      }
    
                      $sql_query = "SELECT refpoints FROM groups WHERE id = '$group'";
                      $result = mysql_query($sql_query);
                      if(mysql_num_rows($result))
                      {
                      //output as long as there are still available fields
                      while($row = mysql_fetch_array($result))
                      {
                      $refpoints = $row['refpoints'];
                      }
                      }
                      else{
                      $refpoints = 0;
                      }
    
                      $update_ref_points = mysql_query("UPDATE users
                      set points=points+$refpoints
                      where id = '$referredby'");
                  }

            
            
            }


            $todaysdate = time();
            $newpasswordone = md5($newpasswordone);
            
            if(isset($_SESSION['ureferral']))
              $referredby = $_SESSION['ureferral'];

            else
             $referredby = "";


            // Set the member in the default group
                      $sql_query = "SELECT id FROM groups WHERE status = 1 && defaultg = 1 ORDER by id ASC LIMIT 1";
                      $result = mysql_query($sql_query);
                      if(mysql_num_rows($result))
                      {
                      //output as long as there are still available fields
                      while($row = mysql_fetch_array($result))
                      {
                        $group = $row['id'];
                      }
                      }else{
                      
                        $query = "SELECT MIN(id) FROM groups"; 
  	                    $result = mysql_query($query);
  	                    while($row = mysql_fetch_array($result)){
                        $group = $row['id'];
                        }

                      }            
            
            
            $result = mysql_query( "insert into users
                        set username = '$newusername',
                        password = '$newpasswordone',
                        email = '$newemailaddress',
                        status = '$userstatus',
                        joindate = '$todaysdate',
                        referredby = '$referredby',
                        ugroup = '$group',
                        confirmation = '$random'");
            
            $includefile = "./templates/$template_directory/thankyou.html";
            }  
          }
}

elseif ($action == "confirmaccount")
    {
    $un = clean_string($_GET['username']);
    $confirmation = clean_string($_GET['confirmation']);
    confirm_account($un,$confirmation,$base_url,$rewrite,$email_confirmation);
    exit;
    }
 
elseif ($action == "accountconfirmed")
    {
      $includefile = "./templates/$template_directory/confirmedaccount.html";
      $pagetitle = "Account Has Been Confirmed";
    }

elseif ($action == "login" && $member_login==1){

  if ($_SESSION['loggedin']!=1)	{

  $username = clean_string($_POST['loginuser']);
  $password = md5(clean_string($_POST['loginpassword']));
  $cookieme = clean_string($_POST['saveme']);
  $frompage = $_SERVER['HTTP_REFERER'];
  login_user($base_url,$username,$password,$frompage,$cookieme,$rewrite);

  }

    else{
    
            header("Location: ".$base_url."index.php"); 
            exit;
    
    
      }  


}
elseif ($action == "logoff")
		{
 
    
    session_unset(); 
    // unset our sessions 

    session_destroy(); 
    // now destory them and remove them from the users browser 

    if (isset($_COOKIE["un"]))
    setcookie("un", "", time()-60000);
    if (isset($_COOKIE["unp"]))
    setcookie("unp", "", time()-60000);
  
    header("Location: ".$base_url."index.php"); 
    // forward you to a page of your choice 
   
		exit;
    }


elseif ($action=="userstats"){

    is_logged_in($base_url,$rewrite);
    
    $includecontent = get_user_stats($template_directory);
      
    $loggedinuser = $_SESSION['loggedinuser'];
    $pagetitle = "$loggedinuser Statistics";

}

elseif($action=="domains"){

    is_logged_in($base_url,$rewrite);
    $_SESSION['deleteconfirmed']="";
    $includecontent = get_user_domains($template_directory,$base_url,$rewrite);

    $loggedinuser = $_SESSION['loggedinuser'];
    $pagetitle = "$loggedinuser Domains";

}

elseif($action=="adddomain"){

    is_logged_in($base_url,$rewrite);

    // Make sure both fields were filled in
    if(empty($_POST['domain']) && empty($_POST['domaintitle'])){

        $_SESSION['status'] = "Please fill out all the fields.";
        if($rewrite==1)
        header("Location: ".$base_url."Manage-Domains.html"); 
        else            
        header("Location: ".$base_url."index.php?action=domains");      
        exit;     
    
    }

    $domain = trim(clean_string($_POST['domain']));
    $domaintitle = clean_string($_POST['domaintitle']);
    


    // Make sure the user only entered in the domain name, no http:// and remove it if its there
    $domain = str_replace("http://","",$domain);    
    $domain = str_replace("www.","",$domain);
    
    // Check to make sure domain isn't already in the system
      $query = "select domain from domains WHERE domain = '$domain%'";
      $result = mysql_query($query);
      if (mysql_num_rows($result)>0)
      {
        $_SESSION['status'] = "Domain already in system";
        if($rewrite==1)
        header("Location: ".$base_url."Manage-Domains.html"); 
        else            
        header("Location: ".$base_url."index.php?action=domains");      
        exit; 
      }

    
    // Checks to make sure submitted domain is not on the banned list
    $baddomains = file("./baddomains.txt");
    foreach($baddomains as $baddomain =>$bd){
    
    
      // If the domain is a bad domain, then let the user know!
      if($domain == trim($bd)){
        
        $_SESSION['status'] = "Invalid Domain Name";
        if($rewrite==1)
        header("Location: ".$base_url."Manage-Domains.html"); 
        else            
        header("Location: ".$base_url."index.php?action=domains");      
        exit; 
      }
      
    }

    $userid = $_SESSION['loggedinuserid'];
    $dateadded = time();
    
    $insert_domain = mysql_query("INSERT into domains
    set domain = '$domain',
    title = '$domaintitle',
    userid = '$userid',
    dateadded = '$dateadded',
    status = '1'");

        $_SESSION['status'] = "Domain Added!";
        if($rewrite==1)
        header("Location: ".$base_url."Manage-Domains.html"); 
        else            
        header("Location: ".$base_url."index.php?action=domains");      
        exit; 


}

elseif($action=="deletedomain"){

    $id = clean_string($_GET['id']);
    
    is_logged_in($base_url,$rewrite);
    $loggedinid = $_SESSION['loggedinuserid'];
    
        $sql_query = "SELECT domain FROM domains WHERE userid = '$loggedinid' && id = $id && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {   
          $domain = $row['domain'];
        }
        }
        else{
        // The user does not have access to that specific domain name
        
            $_SESSION['status'] = "Domain could not be deleted";
            if($rewrite==1)
            header("Location: ".$base_url."Manage-Domains.html"); 
            else            
            header("Location: ".$base_url."index.php?action=domains");      
            exit;         
        
        }
    
    // If the user already confirmed they want to delete, then let's delete it!
    if(!empty($_SESSION['deleteconfirmed']) && ($_SESSION['deleteconfirmed']==$id)){
    
            $delete_domain = mysql_query("DELETE from domains
            where id = '$id'");
            
            $_SESSION['deleteconfirmed']="";
            $_SESSION['status'] = "Domain Deleted!";
            if($rewrite==1)
            header("Location: ".$base_url."Manage-Domains.html"); 
            else            
            header("Location: ".$base_url."index.php?action=domains");      
            exit;         
    
    
    }
    
    // Otherwise we need to display a confirmation page to the user before deleting
    else{
   
            if($rewrite==1){
            
            $deletelink = $base_url."Delete-Domain-$id.html";
            $dontdelete = $base_url."Manage-Domains.html";
            
            }else{
            
            $deletelink = $base_url."index.php?action=deletedomain&id=$id";
            $dontdelete = $base_url."index.php?action=domains";
            
            }
            
            $_SESSION['deleteconfirmed'] = $id;
            $includecontent = "Are you sure you wish to delete <b>$domain</b>?<br/><br/><a href=\"$deletelink\">YES</a> or <a href=\"$dontdelete\">NO</a>";
    
    }
    
    
   

}

elseif($action=="comments"){

      $itemid = clean_string($_GET['id']);
      
      $itemtitle = get_item_title($itemid);
      
      $includefile = "./templates/$template_directory/displaycomments.html";
      
      $pagetitle = "$itemtitle  | Comments";

}

elseif($action=="addcomment"){

  $frompage = $_SERVER['PHP_SELF'];

  if($member_comment==0 || ($member_comment==1 && $_SESSION['loggedin']==1)){

        $itemid = clean_string($_POST['cid']);
        $comment = clean_string($_POST['comment']);

      
        if(empty($comment) || strlen($comment)<5){
          $_SESSION['status'] = "Invalid Comment.";
          header("Location: ".$frompage."");      
          exit;
        }
      
        $dateadded = time();
        $ipaddress = $_SESSION['userip'];
 
        if(!empty($_SESSION['loggedinuserid']))
        $userid = $_SESSION['loggedinuserid'];
        else
        $userid = 0;
      
        $add_comment = mysql_query("INSERT into comments
        set comment = '$comment',
        itemid = '$itemid',
        dateadded = '$dateadded',
        userid = '$userid',
        ipaddress = '$ipaddress',
        status = '1'");

          $_SESSION['status'] = "Comment Added!";
          header("Location: ".$frompage."");      
          exit;  

    }
    else{
    
          $_SESSION['status'] = "You are not allowed to comment.";
          header("Location: ".$frompage."");      
          exit;    
    
    }

}

elseif ($action=="forgotpw" && $member_login==1){

    $includefile = "./templates/$template_directory/forgotpw.html";
    $pagetitle = "$sitename Recover Password";

}


elseif ($action=="changepw"){

    is_logged_in($base_url,$rewrite);
    $includefile = "./templates/$template_directory/changepw.html";
    $pagetitle = "$sitename Change Password";

}

elseif ($action=="updatepw"){

    is_logged_in($base_url,$rewrite);
    if(($_POST['newpw1'] != $_POST['newpw2']) || (empty($_POST['newpw1']) || empty($_POST['newpw2']))){
    $_SESSION['status'] = "Your passwords do not match.";
    }
    else{
    
    $newpassword = md5(clean_string($_POST['newpw1']));
    
    $userid = $_SESSION['loggedinuserid'];
    
    $update_pw = mysql_query("UPDATE users
    set password = '$newpassword'
    where id = '$userid'");
    
    $_SESSION['status'] = "Your password has been updated!";
    
    }    
    
          if($rewrite==1)
          header("Location: ".$base_url."Change-Password.html");      
          else
          header("Location: ".$base_url."index.php?action=changepw");      
          
          exit;    

}

elseif($action=="rate"){

   $frompage = $_SERVER['HTTP_REFERER'];   
   $itemid = clean_string($_GET['id']);
   $rating = clean_string($_GET['rating']);

  if($itemid > 0 && ($rating > 0 || $rating=="yes" || $rating=="no"))
  insert_rating($frompage,$itemid,$rating);
  else{
          
          header("Location: ".$base_url."index.php");               
          exit;  

  }


}

elseif($action=="deletecomment"){

   $frompage = $_SERVER['HTTP_REFERER'];
   
   if ($_SESSION['adminloggedin']==1){

      $commentid = clean_string($_GET['id']);

      $delete_comment = mysql_query("DELETE from comments
      where id = '$commentid'");
   }

          header("Location: ".$frompage."");               
          exit;   

}

else{
    
    // This happens when people are browsing the categories
    if ($c > 0){
        $sql_query = "SELECT title FROM categories WHERE id = '$c'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $categoryname = stripslashes($row['title']);
        }
        }
    
      $categorylink = make_friendly($categoryname);
      $pagetitle = "Browse $categoryname";
    }
    else
      $pagetitle = "Free Link Dump";

    $includefile = "./templates/$template_directory/main.php";

}
// Load the Overall Template

include("./templates/$template_directory/overall.html");


?>
