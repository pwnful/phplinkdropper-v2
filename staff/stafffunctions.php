<?
/*
script: adminfunctions.php
purpose: This file handles functions associated with the admin control panel.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/


function clean_string($string){

    $string = mysql_real_escape_string($string);
    
    return $string;

}

function get_status($string){

    if ($string == 0)
    $status = "Inactive";
    elseif($string == 1)
    $status = "Active";
    elseif($string == 2)
    $status = "<B>Reported</B>";
    elseif($string == 3)
    $status = "Unconfirmed";
    else
    $status = "Unknown";
    
    return $status;

}

function count_items(){

  $sql = "SELECT count(id) AS total_items from items";
  //store the SQL query in the result variable
  $result = @mysql_query($sql);
  $i = mysql_fetch_array($result); 
  $total_items = $i['total_items']; 
  return $total_items;





}

function list_categories($string){

          $sql_query = "SELECT * from categories where status = 1 ORDER by title ASC";
  		    //store the SQL query in the result variable
  		    $result = @mysql_query($sql_query);
  		    if(mysql_num_rows($result))
  		    {
  		    //output as long as there are still available fields
  		    while($row = mysql_fetch_array($result))
  		    {
  		      $categoryname = stripslashes($row['title']);
  		      $categoryid = $row['id'];
  		      
  		      if ($categoryid == $string)
            $content = $content."<option value=\"$categoryid\" selected>$categoryname</option>";
            else
  		      $content = $content."<option value=\"$categoryid\">$categoryname</option>";
          }
          }


          return $content;


}

function count_submissions(){

  $sql = "SELECT count(id) AS total_submissions from items WHERE status = 3";
  //store the SQL query in the result variable
  $result = @mysql_query($sql);
  $i = mysql_fetch_array($result); 
  $total_submissions = $i['total_submissions']; 
  return $total_submissions;





}

function count_reported(){

  $sql = "SELECT count(id) AS total_reported from items WHERE status = 2";
  //store the SQL query in the result variable
  $result = @mysql_query($sql);
  $i = mysql_fetch_array($result); 
  $total_reported = $i['total_reported']; 
  return $total_reported;





}

function google_sitemap($base_url,$rewrite,$trackclicks){

    $sm = fopen("../sitemap.xml","w+");
    $smoutput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">
    ";
    fwrite($sm,$smoutput);
    
    if ($rewrite != 0 && $trackclicks != 0){
    
        $sql_query = "SELECT * FROM items WHERE status = 1";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result))
        {
        //output as long as there are still available fields
        while($row = mysql_fetch_array($result))
        {
                  $mediatitle = ucwords(stripslashes($row['title']));
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
    
                    $smoutput = "
                    <url>
                    <loc>".$itemlink."</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.5</priority>
                    </url>";
                    fwrite($sm,$smoutput);
    
                
        }
        }
    
    }
    
    
    $sql_query2 = "SELECT * FROM categories WHERE status = 1";
    $result2 = mysql_query($sql_query2);
    if(mysql_num_rows($result2))
    {
    //output as long as there are still available fields
    while($row = mysql_fetch_array($result2))
    {
        $catid = $row['id'];
        $catname = $row['title'];
        $catname = stripslashes($catname);
                
        $title2 = strtolower($catname);
        // remove all characters except spaces, a-z and 0-9
        $title2 = ereg_replace("[^a-z0-9 ]", "", $title2);
        // make all spaces single spaces
        $title2 = ereg_replace(" +", " ", $title2);
        // replace spaces with -
        $title2 = str_replace(" ", "_", $title2);
        $title2 = $title2.".html";
			  			  
			  // Determines if the link to the category page is SE Friendly or NOT
			  if ($rewrite ==0)
        $catlink = $base_url."index.php?action=browse&cat=".$catid;
				else
				$catlink = $base_url."categories/".$catid."/".$title2;
			  // End
        
        
        
        
                $smoutput = "
                <url>
                <loc>$catlink</loc>
                <changefreq>weekly</changefreq>
                <priority>0.5</priority>
                </url>";
                fwrite($sm,$smoutput);
        
    
    }
    }
     
    $endstring = "
    </urlset>";
    fwrite($sm,$endstring);
    fclose($sm);
}


function check_security($level){

      
      $stafflevel = $_SESSION['staffsecurity'];
      
      $level = "*".$level."*";
      
      if (strpos($stafflevel,$level)=== false){
      $_SESSION['status'] = "Your Are Now Allowed Access";
      header("Location: index.php");
      exit;
      }
      
      else{
      return;
      }
      
}

function get_category($string){

          $sql_query = "SELECT * from categories where id = $string LIMIT 1";
  		    //store the SQL query in the result variable
  		    $result = @mysql_query($sql_query);
  		    if(mysql_num_rows($result))
  		    {
  		    //output as long as there are still available fields
  		    while($row = mysql_fetch_array($result))
  		    {
  		      $categoryname = stripslashes($row['title']);
          }
          }


          return $categoryname;


}

?>
