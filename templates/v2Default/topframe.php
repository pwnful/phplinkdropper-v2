<? 

session_start();
//error_reporting(0);
include ("../../config.php");
include("../../functions.php");

if ($rewrite == 1)
$reportlink = $base_url."Report/".$_SESSION['contentid'];
else
$reportlink = $base_url."itemout.php?report=".$_SESSION['contentid'];



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$_SESSION['contenttitle'];?> - <?=$sitename;?></title>
<style type="text/css">
<!--
body {
	background-color: #000000;
	margin: 0;
	padding: 0;
}
.itemdesc {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #CCCCCC;
}
.itemwarning {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #FFFFFF;
}
.VoteText {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #FFFFFF;
}


-->
</style></head>

<body>

	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top"><a href="<?=$base_url;?>" target="_blank"><img src="<?=$base_url;?>templates/<?=$template_directory;?>/images/phpLDLogo.gif" border="0"></a></td>
        <td valign="top" width="75%">

</td>
<td>
<span class="VoteText">Rate Link:<br/>
<a class="VoteText" href="<?=$base_url;?>index.php?action=rate&rating=1&id=<?=$_SESSION['contentid'];?>">1</a> <a class="VoteText" href="<?=$base_url;?>index.php?action=rate&rating=2&id=<?=$_SESSION['contentid'];?>">2</a> <a class="VoteText" href="<?=$base_url;?>index.php?action=rate&rating=3&id=<?=$_SESSION['contentid'];?>">3</a> <a class="VoteText" href="<?=$base_url;?>index.php?action=rate&rating=4&id=<?=$_SESSION['contentid'];?>">4</a> <a class="VoteText" href="<?=$base_url;?>index.php?action=rate&rating=5&id=<?=$_SESSION['contentid'];?>">5</a></div>
<br/>
Currently <?=$_SESSION['contentrating'];?>/5.0
<br/><br/>
 <a target = "_top" href="<?=$reportlink;?>"><img src="<?=$base_url;?>images/report.gif" alt="Report Invalid or SPAM Content" width="56" height="15" border="0" />
        </td>
      </tr>
    </table>

</body>
</html>
