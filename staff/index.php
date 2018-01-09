<?php
session_start();
error_reporting(0);
include("../config.php");
include("stafffunctions.php");
$un = clean_string($_POST['un']);
$up = clean_string($_POST['up']);
$id = clean_string($_GET['id']);
$bt = clean_string($_GET['bt']);
$updateid = clean_string($_POST['id']);
$page = clean_string($_GET['page']);
$limit = clean_string($_GET['limit']);
$action = clean_string($_GET['action']);
$order = clean_string($_GET['order']);

function staff_login_box(){
?>
<style type="text/css">
<!--
.adminheader {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FFFF00;
}
.adminfooter {
	font-size: 10px;
	color: #FFFFFF;
}
.FormText {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.PageTitles {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
}
-->
</style><table width = "300" border = "1" align="center" bordercolor = "#000000" cellpadding="0" cellspacing="0"><tr><td>
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		  <tr>
			<td align="center" bgcolor="5464B3"><span class="adminheader">Staff Login</span></td>
		  </tr>
		  <tr>
			<td align="center" valign="top"><? if (!empty($_SESSION['status'])) echo $_SESSION['status']."<BR><BR>";?><form name="form1" method="post" action="index.php">
			  <BR>Username:<input name="un" type="text" id="un">
			  <BR><BR>
			  			  Password:<input name="up" type="password" id="up">
			  			  <BR><BR>
			  			  <input type="submit" name="Submit" value="Login">
			  			  <BR>
		    </form></td>
		  </tr>
		  <tr>
			<td align="center" bgcolor="5464B3"><span class="adminheader">Powered by <a href="http://www.phplinkdropper.com" target="_blank">phpLinkDropper</a></span></td>
		  </tr>
		</table>
</td></tr></table>
<?
exit;
}


function verify_staff($un,$up){

    $up = md5($up);
    $sql = "SELECT * from staff where username = '$un' AND password = '$up' AND status = '1'";
    $result = mysql_query($sql);
    $num = mysql_numrows($result);
    
    if ($num != "0") {
    		$_SESSION['staffloggedin'] = 1;
    		$_SESSION['staffloggedinuser'] = $un;
    		while($row = mysql_fetch_array($result))
        {
          $staffid = $row['id'];
          $stafflevel = $row['security'];
          $_SESSION['staffsecurity'] = $row['security'];
    	  }
         
        $_SESSION['staffid'] = $staffid;
        
        $todaysdate = time();
        $result = mysql_query( "update staff
                            set lastlogin = '$todaysdate',
                            ipaddress = '$ipaddress'
                            where id = '$staffid'");
        
        // Time to create the Staff Menu
        
        if (strpos($stafflevel,"*1*")=== false && strpos($stafflevel,"*2*")=== false){}
        else
        $menu = $menu."<a href=\"index.php?action=manageitems\">Manage Items</a><BR><BR>";
        /*
        if (strpos($stafflevel,"*3*")=== false)
        $menu = $menu."";
        if (strpos($stafflevel,"*4*")=== false)
        $menu = $menu."";
        */
        if (strpos($stafflevel,"*5*")=== false && strpos($stafflevel,"*6*")=== false){}
        else
        $menu = $menu."<a href=\"index.php?action=managesubmissions\">Submissions</a><BR><BR>";
        
        /*
        if (strpos($stafflevel,"*7*")=== true)
        $menu = $menu."";
        */
         $_SESSION['menu'] = $menu;
        // End Menu Creation
        
        
        
        
        
        
        
        
        
        header('Location: index.php');
        exit;
        }
    
    else { 
        $_SESSION['status'] = "Invalid Username and/or Password";
        header("Location: index.php");
       } 
  
  


}




if ($_SESSION['staffloggedin'] != '1' && (!empty($un) && (!empty($up))))
verify_staff($un,$up);
elseif ($_SESSION['staffloggedin'] != '1')
staff_login_box();



if($action == "managesubmissions"){
// Brings up the submission manager


  $includefile = "managesubmissions.php";
}

elseif($action == "confirmsubmission"){
// Confirms the submission
  check_security(5);

  $confirm_submission = mysql_query("UPDATE items
                                    set status = '1'
                                    where id = '$id'");
  $_SESSION['status'] = "Submission Confirmed";
  $includefile = "managesubmissions.php";
  
}

elseif($action == "rejectsubmission"){
// Rejects the submission
  check_security(6);
  
  $reject_submission = mysql_query("DELETE from items
                                    where id = '$id'");
  $_SESSION['status'] = "Submission Rejected";
  $includefile = "managesubmissions.php";
  
}

elseif($action == "manageitems"){
// Brings up the item manager

  $includefile = "manageitems.php";
}

elseif($action == "edititem"){
// Brings up the EDIT ITEM form
  check_security(1);

          $sql_query = "SELECT * from items WHERE id = $id";
  		    //store the SQL query in the result variable
  		    $result = @mysql_query($sql_query);
  		    if(mysql_num_rows($result))
  		    {
  		    //output as long as there are still available fields
  		    while($row = mysql_fetch_array($result))
  		    {
  		      $itemname = stripslashes($row['title']);
  		      $itemurl = stripslashes($row['url']);
  		      $itemstatus = $row['status'];
  		      $itemcategory = $row['category'];
          }
          }




  $includefile = "edititem.php";
}
elseif($action == "updateitem"){
// Updates the item in the database
  check_security(1);
  
  $itemname = addslashes($_POST['itemname']);
  $itemurl = addslashes($_POST['itemurl']);
  $itemcategory = $_POST['itemcategory'];
  $itemstatus = $_POST['itemstatus'];
  $updateid = $_POST['updateid'];
  
  $update_item = mysql_query("UPDATE items
  set title = '$itemname',
  url = '$itemurl',
  status = '$itemstatus',
  category = '$itemcategory'
  where id = '$updateid'");

  $_SESSION['status'] = "Item Updated";

  if ($reported == "reported")
  header("Location: index.php?action=manageitems&bt=reported"); /* Redirect browser */
  else
  header("Location: index.php?action=manageitems"); /* Redirect browser */
  exit;

}
elseif($action == "deleteitem"){
// Deletes an item from the database
  check_security(2);

  $delete_item = mysql_query("DELETE from items
                                    where id = '$id'");
  $_SESSION['status'] = "Item Deleted";
  
  if ($bt == "reported")
  header("Location: index.php?action=manageitems&bt=reported"); /* Redirect browser */
  else
  header("Location: index.php?action=manageitems"); /* Redirect browser */
  exit;
  
}

elseif($action == "logout"){
// Logout of the admin control panel
  
  session_unset(); 
    // unset our sessions 

  session_destroy(); 
    // now destory them and remove them from the users browser 

  header("Location: index.php"); 
    exit;


}
else{

  $includefile = "main.php";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>phpLinkDropper Control Panel</title>
<style type="text/css">
<!--
body {
	background-color: #818181;
}
.AdminHeader {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #80FF00;
	font-weight: bold; 
}
.AdminFooter {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 10px;
	color: #FFFFFF;
}
.MenuText {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.MenuSmallText {font-size: 11px}
.TableText {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.PageNumbers {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
}
.FormText {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.PageTitles {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
}
.TableHeaderText {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
}
-->
</style>

<script LANGUAGE="JavaScript">
<!--
// Nannette Thacker http://www.shiningstar.net
function confirmDeleteCategory()
{
var agree=confirm("Are you sure you wish to delete this category?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>

<script LANGUAGE="JavaScript">
<!--
// Nannette Thacker http://www.shiningstar.net
function confirmDeleteItem()
{
var agree=confirm("Are you sure you wish to delete this?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
</head>

<body>
<table width="750" border="0" bgcolor="#FFFFFF" align="center" cellpadding="3" cellspacing="0">
  <tr valign="middle" bgcolor="#4E5975">
    <td colspan="2" class="AdminHeader"><a href="http://www.phplinkdropper.com" title="Powered by phpLinkDropper" target="_blank"><img src="toplogo.jpg" border="0"></a></td>
  </tr>
  <tr>
    <td width="140" bgcolor="#E0E0E1" valign="top">
      
      <span class="MenuText"><a href="index.php">Home</a><BR><BR>
      
      <? echo $_SESSION['menu'];?>
      <a href="index.php?action=logout">Logout</a>
      </span>
    
    </td>
    <td width="610" valign="top">
    <? if (!empty($_SESSION['status'])){ echo $_SESSION['status']."<BR>"; $_SESSION['status']="";}?>
    <? if (!empty($includefile)) include("$includefile");?></td>
  </tr>
  <tr align="center" bgcolor="#4E5975">
    <td colspan="2"><span class="AdminFooter">(c) 2006 :: Developed by <a href="http://www.lewiswebdev.com" target="_blank">Lewis Web Developments</a> </span></td>
  </tr>
</table>
</body>
</html>
