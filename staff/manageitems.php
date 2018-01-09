<?

/*
script: manageitems.php
purpose: This is the ITEM manager.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

// Check Security
if ($_SESSION['staffloggedin'] != '1')
staff_login_box();
// End Check 



  if (!empty($abc))
  $searchby = $searchby." && title LIKE \"$abc%\"";
  
  if(!empty($order)){
  
    if ($order == "datedesc")
    $orderby = "ORDER by date DESC";
    elseif ($order == "dateasc")
    $orderby = "ORDER by date ASC";
    elseif ($order == "clicksdesc")
    $orderby = "ORDER by hits DESC";
    elseif ($order == "clicksasc")
    $orderby = "ORDER by hits ASC";
    elseif ($order == "status")
    $orderby = "ORDER by status DESC";
    elseif ($order == "titledesc")
    $orderby = "ORDER by title DESC";
    elseif ($order == "titleasc")
    $orderby = "ORDER by title ASC";
    else
    $orderby = "ORDER BY date DESC";
  }
 





?>


<table width="98%" border="0" align="center" cellpadding="4" cellspacing="0">  
           <tr>
            <td colspan="5"><span class="PageTitles">Manage Items </span></td>
          </tr>
           
           <tr>
            <td valign="top" class="TableHeaderText">Name<BR><a href="index.php?action=manageitems&bt=<?=$bt;?>&order=titleasc" title="Sort Ascending"><img src="icon_asc.gif" border="0"></a> <a href="index.php?action=manageitems&bt=<?=$bt;?>&order=titledesc" title="Sort Descending"><img src="icon_desc.gif" border="0"></a></td>
            <td valign="top" class="TableHeaderText">Clicks<BR><a href="index.php?action=manageitems&bt=<?=$bt;?>&order=clicksasc" title="Sort Ascending"><img src="icon_asc.gif" border="0"></a> <a href="index.php?action=manageitems&bt=<?=$bt;?>&order=clicksdesc" title="Sort Descending"><img src="icon_desc.gif" border="0"></a></td>
            <td valign="top" class="TableHeaderText">Date Added<BR><a href="index.php?action=manageitems&bt=<?=$bt;?>&order=dateasc" title="Sort Ascending"><img src="icon_asc.gif" border="0"></a> <a href="index.php?action=manageitems&bt=<?=$bt;?>&order=datedesc" title="Sort Descending"><img src="icon_desc.gif" border="0"></a></td>
            <td valign="top" class="TableHeaderText">Status<BR><a href="index.php?action=manageitems&bt=<?=$bt;?>&order=statusasc" title="Sort Ascending"><img src="icon_asc.gif" border="0"></a> <a href="index.php?action=manageitems&bt=<?=$bt;?>&order=statusdesc" title="Sort Descending"><img src="icon_desc.gif" border="0"></a></td>
            <td valign="top" class="TableHeaderText">Action</td>
          </tr>

<?



if (!($limit)){
$limit = $max_results;} // Default results per-page.
if (!($page)){
$page = 0;} // Default page value.
if ($bt == "reported")
$sql_query = "SELECT * from items WHERE status = 2 $searchby $orderby";
else
$sql_query = "SELECT * from items WHERE status < 99 $searchby $orderby";
$numresults = mysql_query($sql_query); // the query.
$numrows = mysql_num_rows($numresults); // Number of rows returned from above query.
if ($numrows == 0){

  $namelist = "<tr><td colspan=\"5\"><div align=\"center\">No Items Found</div></td></tr>";


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
    $itemtitle = stripslashes($data['title']);
    if (strlen($itemtitle)>35)
    $itemtitle = substr($itemtitle,0,35)."...";
    $itemid = $data['id'];
    $itemurl = stripslashes($data['url']);
    $itemtitle = "<a href=\"$itemurl\" target=\"_blank\" title=\"Click to view LIVE SUBMISSION\">$itemtitle</a>";
    $itemstatus = stripslashes($data['status']);
    $itemstatus = get_status($itemstatus);
    $itemdate = $data['date'];
    $itemdate = date("M j, Y",$itemdate);
    $itemclicks = $data['hits'];

    if (strpos($_SESSION['staffsecurity'],"*1*")=== false)
    $editlink = "";
    else
    $editlink = "<a href=\"index.php?action=edititem&id=$itemid\" title=\"Edit Item\"><img src=\"icon_edit.gif\" border=\"0\"></a>";
    
    if (strpos($_SESSION['staffsecurity'],"*2*")=== false)
    $deletelink = "";
    else  
    $deletelink = "<a onclick='return confirmDeleteItem()'<a TITLE = 'Delete Item' href='index.php?action=deleteitem&id=".$itemid."'><img src=\"icon_delete.gif\" border=\"0\"></a>";            
       
              $content = $content."<tr onmouseover=\"this.style.backgroundColor='#EBEBEB';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\">
                                        <td class=\"TableText\">$itemtitle</td>
                                        <td class=\"TableText\">$itemclicks</td>
                                        <td class=\"TableText\">$itemdate</td>
                                        <td class=\"TableText\">$itemstatus</td>
                                        <td class=\"TableText\">$editlink / $deletelink</td>
                                    </tr>";
    
    
              
             
    
    
    }
}
echo $content;
?>


<tr>
<td colspan="5" class="PageNumbers"><div align="center">
<p align="center">
<?

           if ($page != 0) { // Don't show back link if current page is first page.
            $back_page = $page - $limit;
            echo ("<a href=\"".$base_url."staff/index.php?action=manageitems&bt=$bt&abc=$abc&order=$order&page=$back_page&limit=$limit\">back</a>    \n");}
        
            for ($i=1; $i <= $pages; $i++) // loop through each page and give link to it.
            {
            $ppage = $limit*($i - 1);
            if ($ppage == $page){
            echo ("<b>$i</b> \n");} // If current page don't give link, just text.
            else{
            echo ("<a href=\"".$base_url."staff/index.php?action=manageitems&bt=$bt&abc=$abc&order=$order&page=$ppage&limit=$limit\">$i</a> \n");}
            }
        
            if (!((($page+$limit) / $limit) >= $pages) && $pages != 1) { // If last page don't give next link.
            $next_page = $page + $limit;
            echo ("    <a href=\"".$base_url."staff/index.php?action=manageitems&bt=$bt&abc=$abc&order=$order&page=$next_page&limit=$limit\">next</a>\n");}



?>
</div></td></tr>

</table>
