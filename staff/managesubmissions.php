<?
/*
script: managesubmissions.php
purpose: This is the SUBMISSION manager.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

// Check Security
if ($_SESSION['staffloggedin'] != '1')
staff_login_box();
// End Check 


?>


<table width="98%" border="0" align="center" cellpadding="4" cellspacing="0">  
           <tr>
            <td colspan=""><span class="PageTitles">Manage Submissions</span></td>
          </tr>
           <tr>
            <td class="TableHeaderText">Name</td>
            <td class="TableHeaderText">Date Added</td>
            <td class="TableHeaderText">Category</td>
            <td class="TableHeaderText">Action</td>
          </tr>

<?


if (!($limit)){
$limit = $max_results;} // Default results per-page.
if (!($page)){
$page = 0;} // Default page value.
$sql_query = "SELECT * from items WHERE status = 3 ORDER BY title ASC";
$numresults = mysql_query($sql_query); // the query.
$numrows = mysql_num_rows($numresults); // Number of rows returned from above query.
if ($numrows == 0){

  $namelist = "<tr><td colspan=\"4\"><div align=\"center\">No Submissions Found</div></td></tr>";


}
else{


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
    
  
    // Now we can display results.
    $results = mysql_query("$sql_query LIMIT $page, $limit");
    $number_of_results = 0;
    while ($data = mysql_fetch_array($results))
    {
    $itemid = $data['id'];
    $itemtitle = stripslashes($data['title']);
    $itemurl = stripslashes($data['url']);
    $itemtitle = "<a href=\"$itemurl\" target=\"_blank\" title=\"Click to view LIVE SUBMISSION\">$itemtitle</a>";
    $itemstatus = stripslashes($data['status']);
    $itemstatus = get_status($itemstatus);
    $itemdate = $data['date'];
    $itemdate = date("M j, Y",$itemdate);
    $itemclicks = $data['hits'];
    $itemcategory = $data['category'];
    $itemcategory = get_category($itemcategory);
    
    if (strpos($_SESSION['staffsecurity'],"*1*")=== false)
    $editlink = "";
    else
    $editlink = "<a href=\"index.php?action=edititem&id=$itemid\" title=\"Edit Item\"><img src=\"icon_edit.gif\" border=\"0\"></a>";
    
    if (strpos($_SESSION['staffsecurity'],"*5*")=== false)
    $confirmlink = "";
    else  
    $confirmlink = "<a href=\"index.php?action=confirmsubmission&id=$itemid\" title=\"Accept Submissions\"><img src=\"icon_accept.gif\" border=\"0\"></a>";            
    
    if (strpos($_SESSION['staffsecurity'],"*6*")=== false)
    $rejectlink = "";
    else  
    $rejectlink = "<a href=\"index.php?action=rejectsubmission&id=$itemid\" title=\"Reject Submission\"><img src=\"icon_reject.gif\" border=\"0\"></a>";            
      
            
              
              $content = $content."<tr onmouseover=\"this.style.backgroundColor='#EBEBEB';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\">
                                        <td class=\"TableText\">$itemtitle</td>
                                        <td class=\"TableText\">$itemdate</td>
                                        <td class=\"TableText\">$itemcategory</td>
                                        <td class=\"TableText\">$confirmlink / $editlink / $rejectlink</td>
                                    </tr>";
    
    
              
             
    
    
    }
}
echo $content;
?>


<tr>
<td colspan="4" class="PageNumbers"><div align="center">
<p align="center">
<?

           if ($page != 0) { // Don't show back link if current page is first page.
            $back_page = $page - $limit;
            echo ("<a href=\"".$base_url."staff/index.php?action=managesubmissions&page=$back_page&limit=$limit\">back</a>    \n");}
        
            for ($i=1; $i <= $pages; $i++) // loop through each page and give link to it.
            {
            $ppage = $limit*($i - 1);
            if ($ppage == $page){
            echo ("<b>$i</b> \n");} // If current page don't give link, just text.
            else{
            echo ("<a href=\"".$base_url."staff/index.php?action=managesubmissions&page=$ppage&limit=$limit\">$i</a> \n");}
            }
        
            if (!((($page+$limit) / $limit) >= $pages) && $pages != 1) { // If last page don't give next link.
            $next_page = $page + $limit;
            echo ("    <a href=\"".$base_url."staff/index.php?action=managesubmissions&page=$next_page&limit=$limit\">next</a>\n");}



?>
</div></td></tr>

</table>
