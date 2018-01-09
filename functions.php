<?
/*
script: functions.php
purpose: This file handles functions associated with the main script.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

function insert_rating($frompage,$itemid,$rating){

    if($rating > 0 && $rating < 6){
        $insert_rating = mysql_query("UPDATE items
        set votes=votes+1,
        points=points+$rating
        where id = '$itemid'");
    }else{
    
        if($rating=="yes"){
          $insert_rating = mysql_query("UPDATE items
          set yes=yes+1
          where id = '$itemid'");    
        }
        elseif($rating=="no"){
          $insert_rating = mysql_query("UPDATE items
          set no=no+1
          where id = '$itemid'");    
        }
        
    }

    header("Location: ".$frompage.""); 
    exit;

}


function random_image(){

    $files = array();
    $file2 = "";
    $output = "";
    
    if ($handle2 = opendir("./content/images"))
    {
        while (false !== ($file = readdir($handle2)))
        {
            if ($file != "." && $file != "..")
            {
                $files[] = $file;
            }
        }
        closedir($handle2);
    }
    
    $getRand = mt_rand(0, count($files) - 1);
    
    for ($i = 0 ; $i < count($files) ; $i++)
    {
        $file2 = $files[$getRand];
    }
    
        
    return $file2;


}


// This fuction is called after someone puts in their email address to be sent a new password
function send_password($emailaddress,$sitename,$contact_email,$rewrite,$base_url)
{

  $sql_query = "SELECT id,username,email FROM users WHERE email = '$emailaddress' LIMIT 1";
  
  $result = mysql_query($sql_query);
  // If the user entered a email address in the system, then it will pull the user's information
  // and generate a new password
  if(mysql_num_rows($result))
  {
  //output as long as there are still available fields
  while($row = mysql_fetch_array($result))
  {
  $user_id = $row['id'];
  $user_name = $row['username'];
  $user_email = $row['email'];
  
  }
  $alphanum = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
  // generate random password
  $newpassword = substr(str_shuffle($alphanum), 0, 8);
  $encodedpw = md5($newpassword);
  
  // Puts the user's password in the database
  $add_new_password = mysql_query ("update users
                      set password = '$encodedpw'
                      where id = $user_id");
  
  
  // Email message sent to the user with their new password
  $msg .='Below please find your username and password that is registered with '.$sitename.'.<br /><br />';
  $msg .='Username: <strong>'.$user_name.'</strong><br />';
  $msg .='New Password: <strong>'.$newpassword.'</strong><br />';
  $msg .='Feel free to login and change your current password from your EDIT profile section<br />';
  $msg .='Thank you!';


  $to = "$user_email";
  $subject = "Password Recovery";
  $headers ="Return-Path: ".$contact_email."\r\n";
  $headers .= "From: ".$sitename." <".$contact_email.">\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-type: text/html\r\n"; 
  mail($to, $subject, $msg, $headers);

  
  $_SESSION['status']="An email has been sent to your account with your new password. Thank you!";
  

  }

  // If the user entered an email address not in the system, this message is displayed
  else
  {
  $_SESSION['status']="Could not find that email address on file.";
  }


    // User is then redirected to the page confirming their account is now active
    if($rewrite==1)
    header("Location: ".$base_url."Forgot-Password.html"); 
    else
    header("Location: ".$base_url."index.php?action=forgotpw"); 

    exit;


}

function count_comments($itemid){

      $sql = "SELECT count(id) AS commented_on from comments WHERE itemid = '$itemid' && status = 1";
      //store the SQL query in the result variable
      $result = @mysql_query($sql);
      $i = mysql_fetch_array($result); 
      $commented_on = $i['commented_on']; 
      
return $commented_on;

}

function get_username($userid,$default_user){

        $sql_query = "SELECT username FROM users WHERE id = '$userid' && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $username = stripslashes($row['username']);
        }
        }

        else{
        $username = $default_user;
        }

return $username;

}

function get_item_title($itemid){

        $sql_query = "SELECT title FROM items WHERE id = '$itemid' && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $itemtitle = stripslashes($row['title']);
        }
        }

return $itemtitle;

}

function get_group_name($groupid){

        $sql_query = "SELECT title FROM groups WHERE id = '$groupid' && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $grouptitle = stripslashes($row['title']);
        }
        }

return $grouptitle;

}


function is_url($url) { 
    if (!preg_match('#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url)) { 
        return false; 
    } else { 
        return true; 
    } 

} 

function is_logged_in($base_url,$rewrite){

  if($_SESSION['loggedin']!=1){
            
            if($rewrite==1)
            header("Location: ".$base_url."Register.html"); 
            else            
            header("Location: ".$base_url."index.php?action=register");      
            exit;  
   
  }

}

function clean_string($string){

    $string = mysql_real_escape_string($string);
    
    return $string;

}

function make_friendly($string){

          $string = strtolower($string);
  		    // remove all characters except spaces, a-z and 0-9
          $string = ereg_replace("[^a-z0-9 ]", "", $string);
          // make all spaces single spaces
          $string = ereg_replace(" +", " ", $string);
          // replace spaces with -
          $string = str_replace(" ", "-", $string);
          $string = $string.".html";


  return $string;

}

function make_friendly_no_ext($string){

          $string = strtolower($string);
  		    // remove all characters except spaces, a-z and 0-9
          $string = ereg_replace("[^a-z0-9 ]", "", $string);
          // make all spaces single spaces
          $string = ereg_replace(" +", " ", $string);
          // replace spaces with -
          $string = str_replace(" ", "-", $string);



  return $string;

}

function get_item_status($status){

  if($status=0)
    $status="Inactive";
  elseif($status=1)
    $status="Active";
  elseif($status=2)
    $status="Inactive";
  elseif($status=3)
    $status="Waiting for Approval";




return $status;

}


// This fuction is called to log the user's IP address info into the userlog
function log_user()
{
    if ($_SERVER['HTTP_X_FORWARD_FOR']) {
    $myip = $_SERVER['HTTP_X_FORWARD_FOR'];
    } else {
    $myip = $_SERVER['REMOTE_ADDR'];
    }
  
    $_SESSION['userip'] = $myip;
    
    $myid=$_SESSION['loggedinuserid'];
    $myusername = $_SESSION['loggedinuser'];

    // Check to see if the user is a Search Engien
    if(empty($myusername))
        $myusername = se_spider($uagent);
    // END SE Check

    $currentpage = curPageURL();
    $time = time(); 

    $delete_query2 = "SELECT ipaddress,usertime FROM userlog"; 
    $delete_exec2 = mysql_query ($delete_query2); 

    while ($delete_result2 = mysql_fetch_array ($delete_exec2)) { 
      $delete_time2 = $delete_result2["usertime"]; 

        if ($delete_time2 < ($time - 86400)) { 
            $delete_ip = $delete_result2["ipaddress"]; 

          $query2 = "DELETE FROM userlog WHERE ipaddress='$delete_ip'"; 
          $exec2 = mysql_query ($query2); 
         } 
    } 

    $check_query2 = "SELECT ipaddress FROM userlog WHERE ipaddress='$myip'"; 
    $check_exec2 = mysql_query ($check_query2); 
    $check2 = mysql_num_rows ($check_exec2); 

    if ($check2 == 0) { 
      
      $refdomain = $_SESSION['refdomain'];
      $refpage = $_SESSION['refpage'];
      
      $insert_query2 = "INSERT INTO userlog (userid,username,ipaddress,usertime,pageviews,refdomain,refpage,currentpage) VALUES ('$myid','$myusername','$myip','$time','1','$refdomain','$refpage','$location')"; 
      $insert_exec2  = mysql_query ($insert_query2); 
      } 
  
    else { 
        $update_query2 = "UPDATE userlog SET usertime='$time', userid='$myid', username='$myusername', currentpage='$currentpage', pageviews=pageviews+1 WHERE ipaddress='$myip'"; 
        $update_exec2 = mysql_query ($update_query2); 

    } 
    
    
}

function ban_check($type){

    
    if ($_SERVER['HTTP_X_FORWARD_FOR']) {
    $myip = $_SERVER['HTTP_X_FORWARD_FOR'];
    } else {
    $myip = $_SERVER['REMOTE_ADDR'];
    }
    
    /*
    $myip2
    $myip3
    $myip4
    */

    if ($type == "site")
    $check_query2 = "SELECT ipaddress FROM banned WHERE ipaddress='$myip' && type = '2'"; 
    else
    $check_query2 = "SELECT ipaddress FROM banned WHERE ipaddress='$myip'"; 
    $check_exec2 = mysql_query ($check_query2); 
    $check2 = mysql_num_rows ($check_exec2);

    if ($check2 != 0)
    $banned = "banned";
    
    return $banned;

}

function add_link($websitename,$websiteurl,$websitedescription,$emailaddress,$base_url){


  // First need to make sure all the required information is filled out
  
  if (empty($websitename) || empty($websiteurl) || empty($emailaddress)){
    $_SESSION['submitstatus'] = "You have not entered all the required information for your link trade.";
    header("Location: ".$base_url."index.php?action=tradelinks"); /* Redirect browser */
    return;
  }
 
  // End Required Check

  // Now we need to check and see if that URL is already in the database
      $array=parse_url($websiteurl);
      $websiteurl1 = $array['host'];
      $websiteurl2 = str_replace("www.", "", $websiteurl1);
      $websiteurl3 = "http://".trim($websiteurl2);
      $websiteurl4 = "http://www.".$websiteurl2;

      $duplicate_check = "select * from links where url like '$websiteurl1%' || url like '$websiteurl3%' || url like '$websiteurl4%'";
      $duplicate_result = mysql_query($duplicate_check);
      if (mysql_num_rows($duplicate_result)>0){
        $_SESSION['submitstatus'] = "That link is already in our database.";
        header("Location: ".$base_url."index.php?action=tradelinks"); /* Redirect browser */
        exit;
      }
  // End Duplicate Check

  // Time to add the link
  $insert_link = mysql_query("INSERT into links
  set title = '$websitename',
  url = '$websiteurl',
  description = '$websitedescription',
  email = '$emailaddress',
  status = '1'");
  // End Adding Link

return;

}

function login_user($base_url,$username,$password,$frompage,$cookieme,$rewrite){

    $sql = "SELECT id,preapproved,ugroup from users where username = '$username' AND password = '$password' AND status = '1'";
    $result = mysql_query($sql);
    $num = mysql_numrows($result);
    
    if ($num != "0") {
    		$_SESSION['loggedin'] = 1;
    		$_SESSION['loggedinuser'] = $username;
    		while($row = mysql_fetch_array($result))
        {
        $userid = $row['id'];
        $_SESSION['loggedinuserid'] = $userid;
        $usergroup = $row['ugroup'];
        $_SESSION['usergroup'] = $usergroup;
        $_SESSION['preapproved'] = $row['preapproved'];
    	  }
        
        // Get group submission status
        $sql_query = "SELECT approval,maxday,ratio,minhits,minpoints FROM groups WHERE id = '$usergroup' && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $_SESSION['approval'] = $row['approval'];
        $_SESSION['maxday'] = $row['maxday'];
        $_SESSION['ratio'] = $row['ratio'];
        $_SESSION['minhits'] = $row['minhits'];
        $_SESSION['minpoints'] = $row['minpoints'];
        }
        }        
        
        
         
        $_SESSION['loggedinuserid'] = $userid;
        
        // If User wants to keep their info in a cooke then set the cookies
        if ($cookieme == 1){
        // 7776000 = 90 days
        setcookie("un", "$username", time()+7776000);
        setcookie("unp", "$password", time()+7776000);
        }
        // End Cookie Setting
        
        $todaysdate = time();
        $result = mysql_query( "update users
                            set laston = '$todaysdate'
                            where id = '$userid'");
        
        
        header('Location: '.$frompage.'');
        exit;
        }
    
    else { 
       
       if($rewrite==1)
       $newpwlink= $base_url."Forgot-Password.html";
       else
       $newpwlink=$base_url."index.php?action=forgotpw";

       if($rewrite==1)
       $reglink= $base_url."Register.html";
       else
       $reglink=$base_url."index.php?action=register";

       
       $_SESSION['loginstatus'] = "Invalid Login! If you have forgotten your password please <a class=\"LoginStatus\" href=\"".$newpwlink."\">click here to request a new one</a>. If you have not registered yet for an account, feel free to <a class=\"LoginStatus\" href=\"".$reglink."\">click here to register</a>.";
       header('Location: '.$frompage.'');
       exit(); } 


}

function get_user_group($userid){


        $sql_query2 = "SELECT ugroup FROM users WHERE id = $userid";
        $result2 = mysql_query($sql_query2);
        if(mysql_num_rows($result2))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result2))
        {
        
          $groupid = $row['ugroup'];
        
        }
        }

        else
          $groupid = 0;

return $groupid;

}

function get_comments($base_url,$itemid,$template_directory,$commentlimit){

        $CommentTemplate = file_get_contents("./templates/".$template_directory."/comment.html");
        
        if($commentlimit>0)
        $commentlimit = "LIMIT $commentlimit";
        else
        $commentlimit="";

        $sql_query = "SELECT id,userid,comment,dateadded from comments WHERE itemid = '$itemid' ORDER by dateadded DESC $commentlimit";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        
          $commentid = $row['id'];
          $comment = nl2br(stripslashes($row['comment']));
          $commentuser = $row['userid'];
          $commentdate1 = $row['dateadded'];
          
          $commentuser = get_username($commentuser,"Guest");
          
          $commentdate = date("M j",$commentdate1);
          $commenttime =  date("g:i a",$commentdate1);
          
          if ($_SESSION['adminloggedin']==1)
          $deletecomment = "<a href=\"".$base_url."index.php?action=deletecomment&id=$commentid\"><img src=\"".$base_url."images/delete.gif\" border=\"0\"></a>";
          else
          $deletecomment = "";
          
              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%comment%", // Displays the comment
                          "%commentuser%", // Displays the username for the comment
                          "%commentdate%", // Displays the date of the comment
                          "%commenttime%", // Displays the time of the comment
                          "%deletecomment%", // Admin delete comment link
             
              );

             $bodyreplacements = array(

                          "$comment", // Displays the comment
                          "$commentuser", // Displays the username for the comment
                          "$commentdate", // Displays the date of the comment
                          "$commenttime", // Displays the time of the comment
                          "$deletecomment", // Admin delete comment link
                                          
              );

              $content.=str_replace($bodytags,$bodyreplacements,$CommentTemplate);
    
       
        
        }
        }










return $content;

}

function link_item($base_url,$rewrite,$trackclicks,$contentid,$contenttitle2,$contenturl){

          // If you are tracking clicks or framing out is on, then it will use the following links
          if ($trackclicks == 1){
              // If SE FRIENDLY URLS are turned ON it will display the SE FRIENDLY LINK ... ELSE it will display the non-SE Friendly link
              if ($rewrite == 1)
              $contentlink = $base_url."media/".$contentid."/".$contenttitle2;
              else
              $contentlink = $base_url."itemout.php?id=".$contentid;          
          }
          // End tracking clicks links
          // Otherwise it will use the following link
          else{
          
              $contentlink = $contenturl;                    
          
          }




return $contentlink;

}

// This fuction is called to confirm a user's account is EMAIL confirmation is turned on and
// the user clicks on their link in the email
function confirm_account($un,$confirmation,$base_url,$rewrite,$email_confirmation)
{
    $find_user = "SELECT id,status,confirmation,referredby FROM users WHERE username = '$un'";
    $find_user_result = mysql_query($find_user);
    if(mysql_num_rows($find_user_result))
    {
    while($row = mysql_fetch_array($find_user_result))
    {
    $uid = $row['id'];
    $user_status = $row['status'];
    $user_confirmation_number = $row['confirmation'];
    $referredby = $row['referredby'];
    }
    }
    
    // If the user is banned or already confirmed, they will be redirected to the main page
    if ($user_status != 3)
    {
    header("Location: $base_url"); 
    exit;


    }

    // If the confirmation link is invalid, they will be redirected to the main page
    if ($confirmation != $user_confirmation_number)
    {
    header("Location: $base_url"); 
    exit;

    }

    // If the user's confirmation link is correct, the user is confirmed
    $update_status = mysql_query("update users 
    set status= '1'
    where id = $uid");

                  if($referredby>0){
                      
                      
                      $sql_query = "SELECT ugroup FROM users WHERE id = '$referredby'";
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


    if($rewrite==1)
    $outlink = $base_url."Account-Confirmed.html";
    else
    $outlink = $base_url."index.php?action=accountconfirmed";

    // User is then redirected to the page confirming their account is now active
    header("Location: ".$outlink.""); 
    exit;



}

function resize($filename,$width,$height,$newwidth,$newheight)
{

   $format = 'image/jpeg';
   $source = imagecreatefromjpeg($dir_name . $filename);

       $thumb = imagecreatetruecolor($newwidth,$newheight);
       imagealphablending($thumb, false);
       //$source = @imagecreatefromjpeg($dir_name . $filename);
       imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
       $filename=$output_dir . $filename;
       @imagejpeg($thumb, $filename);

return;
}

function credit_user_domain($referral1,$referral2){

        $sql_query = "SELECT id,userid FROM domains WHERE domain = '$referral2' || domain = '$referral1' LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $domainid = $row['id'];
        $domainuser = $row['userid'];
        
        // Now we need to see how many points this user gets
                      $sql_query = "SELECT ugroup FROM users WHERE id = '$domainuser'";
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
    
                      $sql_query = "SELECT inpoints FROM groups WHERE id = '$group'";
                      $result = mysql_query($sql_query);
                      if(mysql_num_rows($result))
                      {
                      //output as long as there are still available fields
                      while($row = mysql_fetch_array($result))
                      {
                      $inpoints = $row['inpoints'];
                      }
                      }
                      else{
                      $inpoints = 0;
                      }

        // Now its time to update the domain and user stats
        $update_domain = mysql_query("UPDATE domains
        set dayin=dayin+1,
        hitsin=hitsin+1
        where id = '$domainid'");
        
        $update_user = mysql_query("UPDATE users
        set points=points+$inpoints
        where id = '$domainuser'");
        }
        }
return;
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


function se_spider(){

      $uagent = $_SERVER['HTTP_USER_AGENT'];     
      if(eregi("googlebot",$uagent)) 
      $content = "GoogleBot";
      elseif(eregi("ia_archiver",$uagent)) 
      $content = "Alexa";      
      elseif(eregi("msnbot",$uagent)) 
      $content = "MSN";      
      elseif(eregi("wget",$uagent)) 
      $content = "WGET :: Possible Leech";      
      elseif(eregi("yahoo",$uagent)) 
      $content = "Yahoo!";      
      elseif(eregi("teomaagent",$uagent)) 
      $content = "Teoma";      
      elseif(eregi("slurp",$uagent)) 
      $content = "Slurp";      
      elseif(eregi("ask jeeves",$uagent)) 
      $content = "Ask Jeeves";      

      if(!empty($content))
      $content="<b>$content</b>";

return $content;

}

function online_users(){
  
  $CurTime = time();
  $CutOff = $CurTime - 600;

  $sql = "SELECT count(ipaddress) AS online_users from userlog WHERE usertime > $CutOff";
  //store the SQL query in the result variable
  $result = mysql_query($sql);
  $i = mysql_fetch_array($result); 
  $online_users = $i['online_users']; 

  return $online_users;

}

?>
