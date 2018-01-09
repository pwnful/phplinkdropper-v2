<?
/*
script: template_functions.php
purpose: This file handles functions associated with the main script.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

function display_image_plugs($base_url,$limit,$rewrite){

        $today = time();
        $featured_query = "SELECT * FROM plugs WHERE status = 1 && (type = 1 || type = 3) && (enddate = 0 || enddate > $today) && (startdate = 0 || startdate < $today) ORDER BY RAND() LIMIT $limit";
		    //store the SQL query in the result variable
		    $result = mysql_query($featured_query);
		    if(mysql_num_rows($result))
		    {
		    while($row = mysql_fetch_array($result))
		    {
        $plugtitle = stripslashes($row['title']);
        $plugtitle2 = make_friendly($plugtitle);
        $plugimage = $row['image'];
        $plugurl = $row['url'];
        $plugid = $row['id'];
        $plugalttext = stripslashes($row['alttext']);

        if ($rewrite == 1)        
        $plugurl = $base_url."plug/".$plugid."/".$plugtitle2;
        else
        $plugurl = $base_url."plugout.php?id=".$plugid;
         
        $featuredcontent = $featuredcontent."<a href=\"$plugurl\" target=\"_blank\" class=\"featured\"><img src=\"$plugimage\" alt=\"$plugalttext\" width=\"50\" height=\"50\" border=\"0\"></a> ";
        }
		    }


        return $featuredcontent;




}

function most_popular($base_url,$trackclicks,$frameout,$rewrite,$default_image,$template_directory,$limit){


  $x = 0;
  $sql_query = "SELECT title,id,url,image from items WHERE status = 1 && image != '' ORDER BY hits DESC LIMIT $limit";
  //store the SQL query in the result variable
  $result = mysql_query($sql_query);
  if(mysql_num_rows($result))
  {
  //output as long as there are still available fields
  while($row = mysql_fetch_array($result))
  {
  $x++;
  $contenttitle = ucwords(stripslashes($row['title']));
  $id = $row['id'];
  $contenturl = $row['url'];
  $contentimage = $row['image'];
    
  $title = make_friendly($contenttitle);
  
  if(empty($contentimage))
  $contentimage = $base_url."templates/".$template_directory."/images/".$default_image;
  else
  $contentimage = $base_url."content/images/".$contentimage;
  
  

  if ($trackclicks == 1 || $frameout == 1){ 
     if ($rewrite == 1)
     $itemlink = $base_url."media/".$id."/".$title;
     else
     $itemlink = $base_url."itemout.php?id=".$id; 
  }
  else{

     $itemlink = $contenturl;                    
  
  }
     $content.="<a href=\"$itemlink\" target=\"_blank\" title=\"$contenttitle\"><img src=\"$contentimage\" alt=\"$contenttitle\" width=\"80\" height=\"80\" style=\"margin-right:4px; margin-top:4px;\" border=\"0\"></a>";
     if ($x==2){
     $content.="<br/>";
     $x=0;
     }

      
      
  }
  }

  return $content;








}

function list_category_feeds($base_url,$trackclicks,$rewrite){

    if ($rewrite != 0 && $trackclicks != 0){
    
        $sql_query2 = "SELECT * FROM categories WHERE status = 1 ORDER by title ASC";
        $result2 = mysql_query($sql_query2);
        if(mysql_num_rows($result2))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result2))
        {
            $catid = $row['id'];
            $catname = $row['title'];
            $catname = stripslashes($catname);
                        
            $feeds = $feeds."- $catname <a href=\"".$base_url."rss.php?type=$catid&limit=15\" title=\"Click to see the $catname Content RSS Feed\"><img src=\"".$base_url."images/rss-feed.jpg\" border=\"0\"></a><BR>";
            
            
        
        }
        }
    
    }

  return $feeds;

}

function latest_searches($base_url,$rewrite,$limit){

  $x = 0;
  $sql_query = "SELECT * from searches WHERE results > 0 ORDER BY date DESC LIMIT $limit";
  //store the SQL query in the result variable
  $result = mysql_query($sql_query);
  if(mysql_num_rows($result))
  {
  //output as long as there are still available fields
  while($row = mysql_fetch_array($result))
  {
  $x++;
  $searchid = $row['id'];
  $searchterm = stripslashes($row['searchterm']);
  
  $title = make_friendly($searchterm);

  
  if (strlen($searchterm>25))
  $searchterm2 = substr($searchterm,0,25)."...";
  else
  $searchterm2 = $searchterm;

     if ($rewrite == 1)
     $itemlink = "<a class=\"mostpopular\" href=\"".$base_url."search-results/".$searchid."/".$title."\" title=\"Click to view search results for $searchterm\">".$searchterm2."</a>";
     else
     $itemlink = "<a class=\"mostpopular\" href=\"".$base_url."index.php?action=search&id=".$searchid."\" title=\"Click to view search results for $searchterm\">".$searchterm2."</a>"; 

     $content = $content.$x." ".$itemlink."<BR>";
      

      
      
  }
  }

  return $content;

}

function submit_category_list(){

        $sql_query2 = "SELECT * FROM categories WHERE status = 1 ORDER by title ASC";
        $result2 = mysql_query($sql_query2);
        if(mysql_num_rows($result2))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result2))
        {
            $catid = $row['id'];
            $catname = $row['title'];
            $catname = stripslashes($catname);
            $content = $content."<option value=\"$catid\">$catname</option>";
        }
        }

  return $content;

}

function cat_jump_list($rewrite,$base_url){ 

    $content = "<form name=\"catlist\">";
    $content = $content.'<select onChange="document.location.href=this[selectedIndex].value" style="font-family: Arial; font-size: 10px; color: #FAF100; padding: 2px; border: 1px solid #666666; background-color: #000000">';
    $content = $content.'<OPTION VALUE="___" selected>Browse By Category</OPTION>';
    $sql_query_categories = "SELECT * FROM categories WHERE status = 1 ORDER BY title ASC";
    //store the SQL query in the result variable
    $result_categories = mysql_query($sql_query_categories);
    if(mysql_num_rows($result_categories))
    {
    //output as long as there are still available fields
    while($row = mysql_fetch_array($result_categories))
    { // Get game ID and Name
          $catid = $row['id'];
          $catname = $row['title'];
          $catname = stripslashes($catname);
          $catname2 = make_friendly($catname);
          
          if ($rewrite ==1)
          $content = $content.'<option value="'.$base_url.'categories/'.$catid.'/'.$catname2.'">'.$catname.'</option>';
          else
          $content = $content. '<option value="'.$base_url.'index.php?c='.$catid.'">'.$catname.'</option>';
    }
    }
    
    $content = $content. '</select>';
    $content = $content. '<noscript><BR><input type="submit" value="GO"></noscript>';
    $content = $content. '</form>';

    return $content;

}

function display_links($base_url,$sortlinks,$realurl){

        if ($sortlinks ==1)
        $orderby = "ORDER BY dayin DESC";
        elseif ($sortlinks ==2)
        $orderby = "ORDER BY totalin DESC";
        elseif ($sortlinks ==3)
        $orderby = "ORDER BY title ASC";
        else
        $orderby = "ORDER BY RAND()";
        
        $sql_query = "SELECT * FROM links WHERE status = 1 $orderby";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
            $linkid = $row['id'];
            $websitename = stripslashes($row['title']);
            $websiteurl = $row['url'];
            $websitedescription = stripslashes($row['description']);
            $totalin = $row['totalin'];
            $totalout = $row['totalout'];
            
            if ($realurl ==1)
            $websitelink = "<a class=\"LinksPage\" href=\"$websiteurl\" target=\"_blank\">$websitename</a>";
            else
            $websitelink = "<a class=\"LinksPage\" href=\"".$base_url."linkout.php?id=$linkid\" target=\"_blank\">$websitename</a>";
            
            $content = $content."$websitelink $websitedescription (IN: $totalin | OUT: $totalout)<BR>";
        }
        }

    return $content;

}

function display_top_links($base_url,$sorttoplinks,$maxtoplinks,$realurl){

        if ($sorttoplinks ==1)
        $orderby = "ORDER BY dayin DESC";
        elseif ($sorttoplinks ==2)
        $orderby = "ORDER BY totalin DESC";
        elseif ($sorttoplinks ==3)
        $orderby = "ORDER BY title ASC";
        else
        $orderby = "ORDER BY RAND()";
        
        $sql_query = "SELECT * FROM links WHERE status = 1 $orderby LIMIT $maxtoplinks";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
            $linkid = $row['id'];
            $websitename = stripslashes($row['title']);
            $websiteurl = $row['url'];
            $websitedescription = stripslashes($row['description']);
            
            // Trims the link name down to 40 characters
            if (strlen($websitename)>25)
            $websitename = substr($websitename,0,25)."...";
            
            $totalin = $row['totalin'];
            $dayin = $row['dayin'];
                       
            if ($realurl ==1)
            $websitelink = "<a title=\"Day In: $dayin / Total In: $totalin\" class=\"TopLinks\" href=\"$websiteurl\" target=\"_blank\">$websitename</a>";
            else
            $websitelink = "<a title=\"Day In: $dayin / Total In: $totalin\" class=\"TopLinks\" href=\"".$base_url."linkout.php?id=$linkid\" target=\"_blank\">$websitename</a>";
            
            $content = $content.$websitelink."<BR>";
        }
        }

    return $content;

}


function get_user_stats($template_directory){

  $StatsTemplate = file_get_contents("./templates/".$template_directory."/userstats.html");
  
  $loggedinid = $_SESSION['loggedinuserid'];
  $username = $_SESSION['loggedinuser'];
  
  // Get the amount of points the user has
  
        $sql_query = "SELECT points,referrals FROM users WHERE id = '$loggedinid'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $userpoints = $row['points'];
        $userreferrals = $row['referrals'];
        }
        }

  // Get the usre's domain stats
        $hitsin = 0;
        $hitsout = 0;
        
        $sql_query = "SELECT hitsin,hitsout FROM domains WHERE userid = '$loggedinid'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
        $domainhitsin = $domainhitsin+$row['hitsin'];
        $domainhitsout = $domainhitsout+$row['hitsout'];
        }
        }
        
        // Get the total number of hits out from user submitted items
        $sql = "SELECT SUM(hits)AS sum_hits FROM `items` WHERE userid = '$loggedinid'"; 
        $result = mysql_query($sql);
        $i = mysql_fetch_array($result);
        $itemhitsout = $i['sum_hits']; 
            
        // Calculate the hits ratio
        $totalhits = $domainhitsin+$domainhitsout;
        if($totalhits<1)  
          $ratio="0%";
        else{
          if($domainhitsout==0)
          $ratio = $domainhitsin*100;
          else
          $ratio = ($domainhitsin/$domainhitsout)*100;
        }

        $ratio = round($ratio,2);

              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%points%", // Number of points the user has
                          "%domainhitsin%", // Number of hits in the user has from their submitted domain(s)
                          "%domainhitsout%", // Number of hits out the user has from their submitted domain(s)
                          "%itemhitsout%", // Number of hits out the user has from their submitted item(s)
                          "%ratio%", // Current user traffic ratio (domain + item combined)
                          "%referrals%", // Number of referrals the user has
                          "%username%", // The username of the user
             
              );

             $bodyreplacements = array(

                          "$userpoints", // Number of points the user has
                          "$domainhitsin", // Number of hits in the user has from their submitted domain(s)
                          "$domainhitsout", // Number of hits out the user has from their submitted domain(s)
                          "$itemhitsout", // Number of hits out the user has from their submitted item(s)
                          "$ratio", // Current user traffic ratio (domain + item combined)
                          "$userreferrals", // Number of referrals the user has
                          "$username", // The username of the user
                                          
              );

              $content.= str_replace($bodytags,$bodyreplacements,$StatsTemplate);
              
              $content.= "<br/>".get_user_items($template_directory);

  return $content;



}

function get_user_items($template_directory){

  $UserItemsTemplate = file_get_contents("./templates/".$template_directory."/useritems.html");
  
  $loggedinid = $_SESSION['loggedinuserid'];
  
  // Get the user's item stats
        $hitsin = 0;
        $hitsout = 0;
        
        $sql_query = "SELECT id,title,url,hits,status,date,votes,points,yes,no FROM items WHERE userid = '$loggedinid' ORDER BY date DESC";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
          $itemurl = $row['url'];
          $itemtitle = stripslashes($row['title']);
          $itemid = $row['id'];
          $itemhits = $row['hits'];
          $itemdate1 = $row['date'];
          $itemstatus = $row['status'];
          
          $itempoints = $row['points'];
          $itemvotes = $row['votes'];
          $itemyes = $row['yes'];
          $itemno = $row['no'];
          
          $itemdate = date("M j Y",$itemdate1);
          $itemtime = date("g:i a",$itemdate1);

          $itemstatus = get_item_status($itemstatus);

              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%itemtitle%", // Title of the item
                          "%itemurl%", // URL to the item
                          "%date%", // Item submit date
                          "%time%", // Iteem submit time
                          "%views%", // Item views
                          "%itemstatus%", // Item status                          
             
              );

             $bodyreplacements = array(

                          "$itemtitle", // Title of the item
                          "$itemurl", // URL to the item
                          "$itemdate", // Item submit date
                          "$itemtime", // Iteem submit time
                          "$itemhits", // Item views
                          "$itemstatus", // Item status
                                                                    
              );

              $content.= str_replace($bodytags,$bodyreplacements,$UserItemsTemplate);


        }
        }
        


  return $content;



}

function get_user_domains($template_directory,$base_url,$rewrite){

  $UserDomainsTemplate = file_get_contents("./templates/".$template_directory."/userdomains.html");
  $UserDomainStatsTemplate = file_get_contents("./templates/".$template_directory."/userdomainstats.html");

  
  $loggedinid = $_SESSION['loggedinuserid'];
  $username = $_SESSION['loggedinuser'];  
  // Get the user's domain stats
        
        $sql_query = "SELECT id,domain,title,hitsin,hitsout,dayin,dayout FROM domains WHERE userid = '$loggedinid' && status = 1 ORDER BY title ASC";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
          $domainid = $row['id'];
          $domain = $row['domain'];
          $domaintitle = stripslashes($row['title']);
          $hitsin = $row['hitsin'];
          $hitsout = $row['hitsout'];
          $dayin = $row['dayin'];
          $dayout = $row['dayout'];
         
          // Calculate the ratios
          if($dayin < 1)
          $dayratio = "0";
          elseif($dayout < 1)
          $dayratio = $dayin*100;
          else
          $dayratio = ($dayin / $dayout)*100;
          
          $dayratio =round($dayratio,2)."%";
          
          if($hitsin < 1)
          $totalratio = "0";
          elseif($hitsout < 1)
          $totalratio = $hitsin*100;
          else
          $totalratio = ($hitsin / $hitsout)*100;
          
          $totalratio = round($totalratio,2)."%";
          
          $deleteicon = $base_url."templates/$template_directory/images/icon_delete.gif";
          
          if($rewrite ==1)
          $deletelink = $base_url."Delete-Domain-$domainid.html";
          else
          $deletelink = $base_url."index.php?action=deletedomain&id=$domainid";

              // Templating Array for displaying each domain and its stats
              
              $bodytags = array(
             
                          "%domain%", // The domain name
                          "%domaintitle%", // Title for the domain the user entered
                          "%totalin%", // Total number of hits in the domain has sent
                          "%totalout%", // Total number of hits out to the domain
                          "%dayin%", // Number of hits in TODAY from the domain
                          "%dayout%", // Number of hits out TODAY to the domain
                          "%dayratio%", // Ratio of hits in/out for TODAY
                          "%totalratio%", // Overall ratio of hits in/out
                          "%deleteicon%", // This will display the DELETE IMAGE ICON
                          "%deletelink%", // This is the link that will be used to delete the domain
                                       
              );

             $bodyreplacements = array(

                          "$domain", // The domain name
                          "$domaintitle", // Title for the domain the user entered
                          "$hitsin", // Total number of hits in the domain has sent
                          "$hitsout", // Total number of hits out to the domain
                          "$dayin", // Number of hits in TODAY from the domain
                          "$dayout", // Number of hits out TODAY to the domain
                          "$dayratio", // Ratio of hits in/out for TODAY
                          "$totalratio", // Overall ratio of hits in/out                                                                    
                          "$deleteicon", // This will display the DELETE IMAGE ICON
                          "$deletelink", // This is the link that will be used to delete the domain
              );

              $userdomains.= str_replace($bodytags,$bodyreplacements,$UserDomainStatsTemplate);            

        }
        }



         // See if there are any status/error messages that need to be displayed on the page
         if(!empty($_SESSION['status'])){
         
         $status = $_SESSION['status']."<br/>";
         $_SESSION['status']="";
         }

              // Templating Array for displaying
              
              $bodytags = array(
             
                          "%username%", // The username of the user
                          "%status%", // Displays the status (and error messages)
                          "%userdomains%", // List of the user's domains
             
              );

             $bodyreplacements = array(

                          "$username", // The username of the user
                          "$status", // Displays the status (and error messages)
                          "$userdomains", // List of the user's domains
                                                                    
              );

              $content.= str_replace($bodytags,$bodyreplacements,$UserDomainsTemplate);
        


  return $content;



}

function display_comment_item($itemid,$template_directory,$base_url,$default_user,$rewrite,$frameout,$default_image){

  $CommentItemTemplate = file_get_contents("./templates/".$template_directory."/commentitem.html");

        $sql_query = "SELECT * FROM items WHERE id = $itemid && status = 1 LIMIT 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
          $itemurl = $row['url'];
          $itemtitle = stripslashes($row['title']);
          $itemdescription = nl2br(stripslashes($row['description']));
          $itemimage = $row['image'];
          $itemhits = $row['hits'];
          $itemdate1 = $row['date'];
          $itemcategory = $row['category'];
          $itempoints = $row['points'];
          $itemvotes = $row['votes'];
          $itemyes = $row['yes'];
          $itemno = $row['no'];
          $itemuser = $row['userid'];

          $itemuser = get_username($itemuser,$default_user);

          $itemtitle2 = make_friendly($itemtitle);

          
          $itemdate = date("M j Y",$itemdate1);
          $itemtime = date("g:i a",$itemdate1);

          // Get Category Name
          
                  $category_query = "SELECT title,defaultimage FROM categories where id = $itemcategory && status = 1 LIMIT 1";
          		    //store the SQL query in the result variable
          		    $result = mysql_query($category_query);
          		    if(mysql_num_rows($result))
          		    {
          		    while($row = mysql_fetch_array($result))
          		    {
                  $itemcategorytitle = stripslashes($row['title']);
                  $itemcategorydefaultimage = $row['defaultimage'];
                  }
          		    }
                  else
                  $itemcategorytitle = "Media";
                  
                  if(empty($itemcategorydefaultimage))
                  $itemcategorydefaultimage = $base_url."templates/".$template_directory."/images/".$default_image;
                  else
                  $itemcategorydefaultimage = $base_url."templates/".$template_directory."/images/".$itemcategorydefaultimage;                  
                  
                  if(empty($itemimage))
                  $itemimage = $itemcategorydefaultimage;
                  else
                  $itemimage = $base_url."content/images/".$itemimage;                  
                  
                  $itemcategorytitle2 = make_friendly($itemcategorytitle);
          // End getting Category Name


              // Make the Category for the Item Hyperlinked
              if ($itemcategory>0){
              
                if($rewrite==1)
                $itemcategorylink=$base_url."categories/$itemcategory/$itemcategorytitle2";
                else
                $itemcategorylink=$base_url."index.php?c=$itemcategory";
              
              }                      
          
              $itemouturl = link_item($base_url,$rewrite,$trackclicks,$itemid,$itemtitle2,$itemurl);  

              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%itemtitle%", // Title of the item
                          "%itemurl%", // URL to the item
                          "%itemouturl%", // OUT URL to the item
                          "%date%", // Item submit date
                          "%time%", // Iteem submit time
                          "%views%", // Item views
                          "%itemdescription%", // Item description
                          "%itemimage%", // Item Image
                          "%category%", // Item Category
                          "%categorylink%", // Item Category URL
                          "%submittedby%", // Username of who submitted the item
                        
             
              );

             $bodyreplacements = array(

                          "$itemtitle", // Title of the item
                          "$itemurl", // URL to the item
                          "$itemouturl", // OUT URL to the item
                          "$itemdate", // Item submit date
                          "$itemtime", // Iteem submit time
                          "$itemhits", // Item views
                          "$itemdescription", // Item description
                          "$itemimage", // Item Image
                          "$itemcategorytitle", // Item Category
                          "$itemcategorylink", // Item Category URL                                                                    
                          "$itemuser", // Username of who submitted the item

              );

              $content.= str_replace($bodytags,$bodyreplacements,$CommentItemTemplate);


        }
        }
        


  return $content;
}

?>
