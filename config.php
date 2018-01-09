<?php

$phpld = "2.0";

// Database Information
// MySQL Host (usually localhost)
$mysql_host = "localhost";
// MySQL Database Name
$database_name = "";
// MySQL User Name
$database_user = "";
// MySQL User Password
$user_password = "";

$connection = @mysql_connect ($mysql_host,$database_user, $user_password) or die ("Cannot make the connection");
//connect to database
$db = @mysql_select_db ($database_name,$connection) or die ("Cannot connect to database.");


/* You can only use one of the following, not both
however you don't have to use either one and the system
will default to using phpLD's normal system of tracking */
$uniqueinout = ""; // Set to 1 if you want to track UNIQUE IN / OUT
$rawinout = ""; // Set to 1 if you want to track RAW IN / OUT

$now = time();
if ($now - $_SESSION['cache_time'] > $cachetime || empty($_SESSION['cache_time']) || $_SESSION['adminloggedin'] == 1){

  $_SESSION['cache_time'] = time();

// Pulls all the Site Config data from the database
        $query = "select * from siteconfig";
        $result = mysql_query($query);
        if(mysql_num_rows($result))
		    {
		    while($row = mysql_fetch_array($result))
		    {
          $base_url = $row['baseurl'];
          $_SESSION['base_url'] = $base_url;
          $rewrite = $row['sefriendly'];
          $_SESSION['rewrite'] = $rewrite;
          $sitename = $row['sitename'];
          $_SESSION['sitename'] = $sitename;
          $sitekeywords = $row['keywords'];
          $_SESSION['sitekeywords'] = $sitekeywords;
          $sitedescription = $row['description'];
          $_SESSION['sitedescription'] = $sitedescription;
          $frameout = $row['frameout'];
          $_SESSION['frameout'] = $frameout;
          $submissions = $row['submissions'];
          $_SESSION['submissions'] = $submissions;
          $trackclicks = $row['trackclicks'];
          $_SESSION['trackclicks'] = $trackclicks;
          $logusers = $row['logusers'];
          $_SESSION['logusers'] = $logusers;
          $reports = $row['reports'];
          $_SESSION['reports'] = $reports;
          $maxpersite = $row['maxpersite'];
          $_SESSION['maxpersite'] = $maxpersite;
          $max_results = $row['maxresults'];
          $_SESSION['max_results'] = $max_results;
          $bannedmessage = $row['bannedmessage'];
          $_SESSION['bannedmessage'] = $bannedmessage;
          $delay = $row['delay'];
          $_SESSION['delay'] = $delay;
          $sitestatus = $row['sitestatus'];
          $_SESSION['sitestatus'] = $sitestatus;
          $offlinemessage = stripslashes(nl2br($row['offlinemessage']));
          $_SESSION['offlinemessage'] = $offlinemessage;
          $prune = $row['prune'];
          $_SESSION['prune'] = $prune;
          $maxtextplugs = $row['maxtextplugs'];
          $_SESSION['maxtextplugs'] = $maxtextplugs;
          $maximageplugs = $row['maximageplugs'];
          $_SESSION['maximageplugs'] = $maximageplugs;
          $linktrading = $row['linktrading'];
          $_SESSION['linktrading'] = $linktrading;
          $maxtoplinks = $row['maxtoplinks'];
          $_SESSION['maxtoplinks'] = $maxtoplinks;
          $sorttoplinks = $row['sorttoplinks'];
          $_SESSION['sorttoplinks'] = $sorttoplinks;
          $sortlinks = $row['sortlinks'];       
          $_SESSION['sortlinks'] = $row['sortlinks'];
          $realurl = $row['realurl'];
          $_SESSION['realurl'] = $realurl;
          $default_user = stripslashes($row['defaultusername']);
          $_SESSION['defaultuser'] = $default_user;
          $member_comment = $row['membercomment'];
          $_SESSION['membercomment'] = $member_comment;
          $member_submit = $row['membersubmit'];
          $_SESSION['membersubmit'] = $member_submit;
          $email_confirmation = $row['emailconfirmation'];
          $_SESSION['emailconfirmation'] = $email_confirmation;
          $require_thumb = $row['requirethumb'];
          $_SESSION['requirethumb'] = $require_thumb;
          $auto_chmod = $row['autochmod'];
          $_SESSION['autochmod'] = $auto_chmod;
          $max_thumb_height = $row['maxthumbheight'];
          $_SESSION['maxthumbheight'] = $max_thumb_height;
          $max_thumb_width = $row['maxthumbwidth'];
          $_SESSION['maxthumbwidth'] = $max_thumb_width;
          $default_image = $row['defaultimage'];
          $_SESSION['defaultimage'] = $default_image;
          $max_publish = $row['maxpublish'];
          $_SESSION['maxpublish'] = $max_publish;
          $publishtime = $row['publishtime'];
          $_SESSION['publishtime'] = $publishtime;
          $contact_email = $row['contactemail'];
          $_SESSION['contactemail'] = $contact_email;
          $cachetime = $row['cachetime'];
          $_SESSION['cachetime'] = $cachetime;
          $member_login = $row['memberlogin'];
          $_SESSION['memberlogin'] = $member_login;
          $max_comments = $row['maxcomments'];
          $_SESSION['maxcomments'] = $max_comments;
          $requirethumb = $row['requirethumb'];
          $_SESSION['requirethumb'] = $requirethumb;
          $skim = $row['skim'];
          $_SESSION['skim'] = $skim;
          $skimurl = $row['skimurl'];
          $_SESSION['skimurl'] = $skimurl;
          $template_directory = $row['templatedirectory'];
          $_SESSION['templatedirectory'] = $template_directory;
          
        }
		    }
        else{
        die;
        }

}else{
          $base_url = $_SESSION['base_url'];

          $rewrite = $_SESSION['rewrite'];

          $sitename = $_SESSION['sitename'];

          $sitekeywords = $_SESSION['sitekeywords'];
          
          $sitedescription = $_SESSION['sitedescription'];
          
          $frameout = $_SESSION['frameout'];
          
          $submissions = $_SESSION['submissions'];
          
          $trackclicks = $_SESSION['trackclicks'];
          
          $logusers = $_SESSION['logusers'];
          
          $reports = $_SESSION['reports'];
          
          $maxpersite = $_SESSION['maxpersite'];
          
          $max_results = $_SESSION['max_results'];
          
          $bannedmessage = $_SESSION['bannedmessage'];
          
          $delay = $_SESSION['delay'];
          
          $sitestatus = $_SESSION['sitestatus'];
          
          $offlinemessage = $_SESSION['offlinemessage'];
          
          $prune = $_SESSION['prune'];
          
          $maxtextplugs = $_SESSION['maxtextplugs'];
          
          $maximageplugs = $_SESSION['maximageplugs'];
          
          $linktrading = $_SESSION['linktrading'];
          
          $maxtoplinks = $_SESSION['maxtoplinks'];
          
          $sorttoplinks = $_SESSION['sorttoplinks'];
          
          $sortlinks = $_SESSION['sortlinks'];       
          
          $realurl = $_SESSION['realurl'];

          $default_user = $_SESSION['defaultusername'];

          $member_comment = $_SESSION['membercomment'];

          $member_submit = $_SESSION['membersubmit'];

          $email_confirmation = $_SESSION['emailconfirmation'];

          $require_thumb = $_SESSION['requirethumb'];

          $auto_chmod = $_SESSION['autochmod'];

          $max_thumb_height = $_SESSION['maxthumbheight'];

          $max_thumb_width = $_SESSION['maxthumbwidth'];

          $default_image = $_SESSION['defaultimage'];

          $max_publish = $_SESSION['maxpublish'];

          $publishtime = $_SESSION['publishtime'];

          $contact_email = $_SESSION['contactemail'];

          $cachetime = $_SESSION['cachetime'];

          $member_login = $_SESSION['memberlogin'];

          $max_comments = $_SESSION['maxcomments'];

          $requirethumb = $_SESSION['requirethumb'];
          
          $skim = $_SESSION['skim'];
          
          $skimurl = $_SESSION['skimurl'];

          $template_directory = $_SESSION['templatedirectory'];

}

return;
?>
