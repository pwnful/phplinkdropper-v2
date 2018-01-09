<?php 
/*
script: main.php
purpose: This script handles the displaying of all the content.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

// NOW THE SCRIPT WILL GET IMAGE PLUGS FOR THE LISTING
if ($maximageplugs > 0){

// Get the Featured Item Template

$FeaturedTemplate = file_get_contents("./templates/".$template_directory."/featured.html");

    // The following determines where the IMAGE PLUGS will be shown and gets the ADS.
    // Below it gets the result numbers to display ads at.
    // $af is the earliest the first PLUG can be display in the results
    // $al is the latest the first PLUG can be displayed in the results.
    // $pn is how we will count the number of plugs displayed
    // $plugpos will hold all the PLUGS for the results in one variable
    
    $today = time();

    $adplace = rand(1,3);
    // End getting AD NUMBERS
    // Now time to get the ADS
    
    if ($c > 0)
    $plug_query = "SELECT * from plugs WHERE status = 1 && (type = 1 || type = 3) && (categories LIKE '%*$c*%' || categories LIKE '%*0*%') && (enddate = 0 || enddate > $today) && (startdate = 0 || startdate < $today) ORDER BY RAND() LIMIT $maximageplugs";
    else
    $plug_query = "SELECT * from plugs WHERE status = 1 && (type = 1 || type = 3) && (enddate = 0 || enddate > $today) && (startdate = 0 || startdate < $today) ORDER BY RAND() LIMIT $maximageplugs";
    
        $plugnum = 0;
        $plug_result = mysql_query($plug_query);
		    if(mysql_num_rows($plug_result))
		    {
		    while($row = mysql_fetch_array($plug_result))
		    {
          $pluglinktitle = "";       
          $plugnum++;
          $plugid = $row['id'];
          $plugtitle = stripslashes($row['title']);
          $plugtitle2 = make_friendly($plugtitle);
          $plugurl = stripslashes($row['url']);
          $plugclicks = $row['clicks'];
          $plugimage = $row['image'];
          $plugcategories = $row['categories'];
          $plugrealclicks = $row['realclicks'];
          $plugalttext = stripslashes($row['alttext']);
          $plugdescription = stripslashes($row['description']);
          $plugimpressions = $row['impressions'];
          $plugmaximp = $row['maximp'];
          
          $plugimpressions++;
          
          $update_plug_imp = mysql_query("UPDATE plugs
          set impressions=impressions+1
          where id = '$plugid'");
          
          // If the plug had a max impression amount and its been met, time it to inactive
          if($plugmaximp>0 && $plugimpressions>=$plugmaximp){
          
          $update_plug_imp = mysql_query("UPDATE plugs
          set status = '0'
          where id = '$plugid'");         
          
          }
          

          // This determines if this plug is a universal plug. If it isn't, it will pick
          // one of the categories the plug is in
          if (strpos($plugcategories,"*0*")=== false){
          
            $plugcategories = explode('*',$plugcategories);
            shuffle($plugcategories);
            $plugcategory = $plugcategories[1];
          } 
          else {$plugcategory = 0;}


          /* If you want to limit the number of characters to display in a title
          just uncomment this page. The example below will limit the number of characters
          in a title to 60
          */
          
          if (strlen($plugtitle)>50)
          $plugtitle = substr($plugtitle,0,50)."...";
          
  
          
          // The following get the first 4 words from the content title so we can hyperlink only
          // those words. If comment this section and uncomment the section right below it you 
          // can have the whole title by hyperlinked.

          
          /*
          $array_of_words = explode(" ",$plugtitle);
          for ($w = 0;$w < 4;$w++)
          $pluglinktitle = $pluglinktitle.$array_of_words[$w]." ";
          $pluglinktitle2 = str_replace(trim($pluglinktitle),"",$plugtitle);
          $pluglinktitle = strtoupper($pluglinktitle);
          
          */
          
          // Comment above to not only hyperlink the first 3 words
          
          // Uncomment the line below to link the whole title
          
          // $pluglinktitle = $plugtitle;
          
          // End
          
                    // If we are browsing a category, then use the category's icon
                    if ($c > 0)
                      $category_title_query = "SELECT id,title,defaultimage FROM categories where id = $c && status = 1 LIMIT 1";
              		  else{
              		    // Need to find an icon for ONE of the categories for the plug
                      if ($plugcategory == 0)
                      $category_title_query = "SELECT id,title,defaultimage FROM categories where status = 1 ORDER BY RAND() LIMIT 1";
                      else            
                      $category_title_query = "SELECT id,title,defaultimage FROM categories where id = $plugcategory && status = 1 LIMIT 1";
                    }
                      //store the SQL query in the result variable
              		    $category_title_result = mysql_query($category_title_query);
              		    if(mysql_num_rows($category_title_result))
              		    {
              		    while($row = mysql_fetch_array($category_title_result))
              		    {
                        $plugcategory = stripslashes($row['title']);
                        $plugcategoryid = $row['id'];
                        $plugdefaultimage = $row['defaultimage'];

                      }
              		    }
                      else
                        $plugcategory = "Plug";

                      if(!empty($plugdefaultimage))
                      $plugdefaultimage = $base_url."templates/".$template_directory."/images/".$plugdefaultimage;                      
                      else
                      $plugdefaultimage = $base_url."templates/".$template_directory."/images/".$default_image;
                      
                      
                      if(empty($plugicon))
                      $plugicon = $plugdefaultimage;
                      
                      $plugcategory2 = make_friendly($plugcategory);

          
              // If SE FRIENDLY URLS are turned ON it will display the SE FRIENDLY LINK ... ELSE it will display the non-SE Friendly link
              if ($rewrite == 1)
              $pluglink = $base_url."plug/".$plugid."/".$plugtitle2;
              else
              $pluglink = $base_url."plugout.php?id=".$plugid;
              
              // Make the Category for the plug Hyperlinked
              if ($plugcategoryid>0){
              
                if($rewrite==1)
                $plugcategorylink=$base_url."categories/$plugcategoryid/$plugcategory2";
                else
                $plugcategorylink=$base_url."index.php?c=$plugcategoryid";
              
              }              
                        
    
              // $plughits is the number of hits a link has. If tracking clicks is turned on, the link will be formatted like
              // the one below
              if ($trackclicks == 1 && $plugrealclicks){
              
                  // If we are using the FAKE NUMBER of clicks we need to create the fake number
                  $plugclicks = rand(72,895);
                  // End fake number of clicks

              }
              
              $plugdate1 = time() - (rand(100,29000));
          
              $plugdate = date("M j",$plugdate1);
              $plugtime =  date("g:i a",$plugdate1);
              

              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%itemtitle%", // Title of the item
                          "%itemdescription%", // Description of the item
                          "%itemurl%", // Hyperlink to the item
                          "%itemimage%", // Image for the item
                          "%category%", // Category for the item
                          "%date%", // Date for the item
                          "%time%", // Time for the item
                          "%views%", // Number of times the item has been viewed
                          "%categorylink%", // Hyperlink to the Category
             
              );

             $bodyreplacements = array(

                          "$plugtitle", // Title of the item
                          "$plugdescription", // Description of the item
                          "$pluglink", // Hyperlink to the item
                          "$plugimage", // Image for the item
                          "$plugcategory", // Category for the item
                          "$plugdate", // Date for the item
                          "$plugtime", // Time for the item
                          "$plugclicks", // Number of times the item has been viewed              
                          "$plugcategorylink", // Hyperlink to the Category
                                          
              );

              $plugcontent.$plugnum = str_replace($bodytags,$bodyreplacements,$FeaturedTemplate);
              
              
              // Time to create the link for the PLUG
              $plugcontentlist.=$plugcontent.$plugnum;
              // End the link display of the PLUG

        }
        }
        

}
// END GETTING PLUGS




$number_of_results=0;

// This determines if we are browsing by all games or a specific category. The first one
// means since they isn't a category id specified we are going to browse all games

if (!($limit)){
$limit = $max_results;} // Default results per-page.
if (!($page)){
$page = 0;} // Default page value.


    // If the user is searching for content
    if (!empty($searchid)){
    
      // Now need to find out what they were searching for
            $search_query = "SELECT searchterm FROM searches WHERE id = '$searchid'";
            $result = mysql_query($search_query);
            if(mysql_num_rows($result))
            {
            //output as long as there are still available fields
            while($row = mysql_fetch_array($result))
            {
            $searchterm = stripslashes($row['searchterm']);
            }
            }
            else{
            // If that isn't a valid search, just browse content
            
                  $sql_query = "SELECT * from items WHERE status = '1' ORDER by date DESC";
            
            // End 
            
            }
      // End finding the search term
    
      $sql_query = "SELECT * from items WHERE status = '1' && title LIKE '%$searchterm%' || description LIKE '%$searchterm%' ORDER by date DESC";
    
    }
    // End Search Content
    
    // If the user is browsing a category
    elseif(!empty($c))
      $sql_query = "SELECT * from items WHERE status = '1' && category = '$c' ORDER by date DESC";
    // End Browsing Category
    
    // Otherwise just browse the database
    else
      $sql_query = "SELECT * from items WHERE status = '1' ORDER by date DESC";
    // End browsing database


$numresults = mysql_query($sql_query); // the query.
$numrows = mysql_num_rows($numresults); // Number of rows returned from above query.
if ($numrows == 0){

  echo "<tr><td colspan=\"2\"><div align=\"center\">No Content Found</div></td></tr></table>";

return;
}

  // If we searched, just need to do a quick update to show the # of results
  if ($searchid > 0){
  
        $update_search = mysql_query("UPDATE searches
        set results = $numrows
        where id = '$searchid'");
  }
  
  // End updating search results


$pages = intval($numrows/$limit); // Number of results pages.

// $pages now contains int of pages, unless there is a remainder from division.

if ($numrows%$limit) {
$pages++;} // has remainder so add one page

$current = ($page/$limit) + 1; // Current page number.

if (($pages < 1) || ($pages == 0)) {
$total = 1;} // If $pages is less than one or equal to 0, total pages is 1.

else {
$total = $pages;} // Else total pages is $pages value.

$first = $page + 1; // The first result.

if (!((($page + $limit) / $limit) >= $pages) && $pages != 1) {
$last = $page + $limit;} //If not last results page, last result equals $page plus $limit.
 
else{
$last = $numrows;} // If last results page, last result equals total number of results.

//escape from PHP mode.
?>


<?
//Go back into PHP mode.

// Get the ITEM template
$ItemTemplate = file_get_contents("./templates/".$template_directory."/item.html");


// Now we can display results.
$results = mysql_query("$sql_query LIMIT $page, $limit");
$number_of_results = 0;
while ($data = mysql_fetch_array($results))
{

$linktitle = "";

$number_of_results++;
$contenturl = $data['url'];
$contenttitle = stripslashes($data['title']);
$contentdescription = stripslashes($data['description']);
$contentid = $data['id'];
$contenthits = $data['hits'];
$contentimage = $data['image'];
$contentdate1 = $data['date'];

$contentuser = $data['userid'];

$contentuser = get_username($contentuser,$default_user);


$contentdate = date("M j",$contentdate1);
$contenttime =  date("g:i a",$contentdate1);

$category = $data['category'];

$comments = count_comments($contentid);

$contentyes = $data['yes'];
$contentno = $data['no'];

$contentvotes = $data['votes'];
$contentpoints = $data['points'];

if($contentvotes >0)
$contentrating = ($contentpoints/$contentvotes);
else
$contentrating = 0;


// Get Category Name

        $category_query = "SELECT title,defaultimage FROM categories where id = $category && status = 1 LIMIT 1";
		    //store the SQL query in the result variable
		    $result = mysql_query($category_query);
		    if(mysql_num_rows($result))
		    {
		    while($row = mysql_fetch_array($result))
		    {
        $categorytitle = stripslashes($row['title']);
        $categoryid = $category;
        $categorydefaultimage = $row['defaultimage'];
        }
		    }
        else
        $categorytitle = "Media";

        if(!empty($categorydefaultimage))
        $categorydefaultimage = $base_url."templates/".$template_directory."/images/".$categorydefaultimage;
        else
        $categorydefaultimage = $base_url."templates/".$template_directory."/images/".$default_image;        

        if(empty($contentimage))
        $contentimage = $categorydefaultimage;
        else        
        $contentimage = $base_url."content/images/".$contentimage;
        
        $categorytitle2 = make_friendly($categorytitle);
// End getting Category Name



          $contenttitle2 = make_friendly($contenttitle);

          /* If you want to limit the number of characters to display in a title
          just uncomment this page. The example below will limit the number of characters
          in a title to 60
          */
          
          
          if (strlen($contenttitle)>60)
          $contenttitle = substr($contenttitle,0,60)."...";

          if (strlen($contentdescription)>300)
          $contentdescription = substr($contentdescription,0,300)."...";          
          
          
          // The following get the first 4 words from the content title so we can hyperlink only
          // those words. If comment this section and uncomment the section right below it you 
          // can have the whole title by hyperlinked.

/*

          $array_of_words = explode(" ",$contenttitle);
          for ($w = 0;$w < 4;$w++){
          $linktitle = $linktitle.$array_of_words[$w]." ";
          
          }
          $linktitle2 = str_replace(trim($linktitle),"",$contenttitle);
          
*/          
          // $linktitle = strtoupper($linktitle);


          // Comment above to not only hyperlink the first 3 words
          
          // Uncomment the line below to link the whole title
          
          // $linktitle = $contenttitle;
          
          // End
          
          
              // Make the Category for the Item Hyperlinked
              if ($categoryid>0){
              
                if($rewrite==1)
                $categorylink2=$base_url."categories/$categoryid/$categorytitle2";
                else
                $categorylink2=$base_url."index.php?c=$categoryid";
              
              }                  
                    
 
              $contentlink = link_item($base_url,$rewrite,$trackclicks,$contentid,$contenttitle2,$contenturl);


              if ($rewrite == 1)
              $commentlink = $base_url."comments/".$contentid."/".$contenttitle2;
              else
              $commentlink = $base_url."index.php?action=comments&id=".$contentid; 
          

              // Templating Array for displaying
              
              $bodytags = array(
              
                          "%itemtitle%", // Title of the item
                          "%itemdescription%", // Description of the item
                          "%itemurl%", // Original Hyperlink to the item
                          "%itemouturl%", // phpLD Hyperlink to the item
                          "%itemimage%", // Image for the item
                          "%category%", // Category for the item
                          "%categorylink%", // Hyperlink to the Category
                          "%date%", // Date for the item
                          "%time%", // Time for the item
                          "%views%", // Number of times the item has been viewed
                          "%yes%", // Number of YES votes
                          "%no%", // Number of NO votes
                          "%votes", // Number of NUMERICAL votes
                          "%points%", // Total number of VOTE POINTS
                          "%rating%", // Rating for Item
                          "%submittedby%", // Username of the person who submitted item
                          "%comments%", // Displays the number of comments
                          "%commentlink%", // The link to the comment display page
             
              );

             $bodyreplacements = array(

                          "$contenttitle", // Title of the item
                          "$contentdescription", // Description of the item
                          "$contenturl", // Submitted Hyperlink to the item
                          "$contentlink", // phpLD Hyperlink to the item                   
                          "$contentimage", // Image for the item
                          "$categorytitle", // Category for the item
                          "$categorylink2", // Hyperlink to the Category
                          "$contentdate", // Date for the item
                          "$contenttime", // Time for the item
                          "$contenthits", // Number of times the item has been viewed              
                          "$contentyes", // Number of YES votes
                          "$contentno", // Number of NO votes
                          "$contentvotes", // Number of NUMERICAL votes
                          "$contentpoints", // Total number of VOTE POINTS
                          "$contentrating", // Rating for Item
                          "$contentuser", // Username of the person who submitted item
                          "$comments", // Displays the number of comments
                          "$commentlink", // The link to the comment display page

              );

       
              $itemContent = str_replace($bodytags,$bodyreplacements,$ItemTemplate);
        
          
          $contentlist.= $itemContent;

/*        UNCOMMENT IF YOU WANT THE PLUGS TO APPEAR IN THE RESULTS
          
          // This part says that if the result number is one of the ad numbers from above
          // then it will display the AD into the results
         
          if (strpos($plugpos,"*$number_of_results*")=== false){}
          else{$pn++;$contentlist = $contentlist.$pluglink[$pn];}
         
         // End displaying PLUG in results


*/

}

  // If SE FRIENDLY URLS are turned ON, these will be the page numbers
  if ($rewrite ==1){
      
      if ($page != 0) { // Don't show back link if current page is first page.
      $back_page = $page - $limit;
      
          if (!empty($searchid))
            $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."search-results/$searchid/$back_page-$limit/$searchlink\">previous</a>    \n");
          elseif (!empty($c))
            $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."categories/$c/$back_page-$limit/$categorylink\">previous</a>    \n");
          else
            $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."archive/$back_page-$limit/\">previous</a>    \n");
      }     
      
       for ($i=1; $i <= $pages; $i++) // loop through each page and give link to it.
       {
       $ppage = $limit*($i - 1);

          if (!empty($searchid)){
           if ($ppage == $page){
             $pagenumbers.=("<b>$i</b> \n");} // If current page don't give link, just text.
             else{
             $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."search-results/$searchid/$ppage-$limit/$searchlink\">$i</a> \n");}
          }
       
         elseif (!empty($c)){
             if ($ppage == $page){
               $pagenumbers.=("<b>$i</b> \n");} // If current page don't give link, just text.
               else{
               $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."categories/$c/$ppage-$limit/$categorylink\">$i</a> \n");}
            }
            
        else{
           if ($ppage == $page){
             $pagenumbers.=("<b>$i</b> \n");} // If current page don't give link, just text.
             else{
             $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."archive/$ppage-$limit/\">$i</a> \n");}
          }
       
       }

      if (!((($page+$limit) / $limit) >= $pages) && $pages != 1) { // If last page don't give next link.
      $next_page = $page + $limit;
      
          if (!empty($searchid))
            $pagenumbers.=("    <a class=\"PageNumbers\" href=\"".$base_url."search-results/$searchid/$next_page-$limit/$searchlink\">next</a>\n");
          elseif (!empty($c))
            $pagenumbers.=("    <a class=\"PageNumbers\" href=\"".$base_url."categories/$c/$next_page-$limit/$categorylink\">next</a>\n");
          else  
            $pagenumbers.=("    <a class=\"PageNumbers\" href=\"".$base_url."archive/$next_page-$limit/\">next</a>\n");
      }      
   }
   
   // Otherwise it will be these non-SE Friendly URLS
   else{
      if ($page != 0) { // Don't show back link if current page is first page.
      $back_page = $page - $limit;
      $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."index.php?searchid=$searchid&c=$c&page=$back_page&limit=$limit\">previous</a>    \n");}

       for ($i=1; $i <= $pages; $i++) // loop through each page and give link to it.
       {
       $ppage = $limit*($i - 1);
       if ($ppage == $page){
       $pagenumbers.=("<b>$i</b> \n");} // If current page don't give link, just text.
       else{
       $pagenumbers.=("<a class=\"PageNumbers\" href=\"".$base_url."index.php?searchid=$searchid&c=$c&page=$ppage&limit=$limit\">$i</a> \n");}
       }

      if (!((($page+$limit) / $limit) >= $pages) && $pages != 1) { // If last page don't give next link.
      $next_page = $page + $limit;
      $pagenumbers.=("    <a class=\"PageNumbers\" href=\"".$base_url."index.php?searchid=$searchid&c=$c&page=$next_page&limit=$limit\">next</a>\n");}
  }

echo $plugcontentlist;
echo $contentlist;
echo $pagenumbers;
?>

