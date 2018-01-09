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
.ae_table_horiz { width:auto; height:auto; background-color:None; }
.ae_td_horiz,.ae_image_td_horiz { padding-bottom: 4px; padding-top: 4px; padding-left: 3px; padding-right: 3px; vertical-align:top; }
.ae_image_td_vert, .ae_image_td_horiz { text-align:right; padding-top:4px; padding-bottom:4px; padding-right: 4px; }
.ae_image_td_sky { text-align:left; padding-top:4px; padding-bottom:0px; padding-right: 0px; }
.ae_bb_td_horiz { padding-bottom: 4px; padding-top: 4px; padding-left: 3px; padding-right: 3px; vertical-align:top; }
A.ae_title_horiz, A.ae_image_link_horiz {font-family: Arial,Sans-Serif; font-size: 12px; font-style: normal; font-weight: bold; font-variant: normal; text-transform: none; color: yellow; text-decoration: None; }
.ae_desc_horiz {font-family: Arial,Sans-Serif; font-size: 12px; font-style: normal; font-weight: normal; font-variant: normal; text-transform: none; color: white; }
.ae_click_count_horiz {font-family: Arial,Sans-Serif; font-size: 10px; font-style: normal; font-weight: normal; font-variant: normal; text-transform: none; color: yellow; }
A.ae_powered_horiz {font-family: Arial,Sans-Serif; font-size: 10px; font-style: normal; font-weight: normal; font-variant: normal; text-transform: none; color: yellow; text-decoration: None; }
A.ae_yourlink_horiz {font-family: Arial,Sans-Serif; font-size: 10px; font-style: normal; font-weight: bold; font-variant: normal; text-transform: none; color: yellow; text-decoration: None; }
A.ae_title_horiz:hover, A.ae_powered_horiz:hover, A.ae_yourlink_horiz:hover, A.ae_image_link_horiz:hover { color: yellow;  text-decoration: Underline;}
.ae_image_horiz { border-width:1px; border-color:yellow;}

-->
</style></head>

<body>

	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top"><a href="<?=$base_url;?>" target="_blank"><img src="<?=$base_url;?>templates/<?=$template_directory;?>/logo2.gif" border="0"></a></td>
        <td valign="top">
<SCRIPT LANGUAGE="JavaScript">
adengage_num_ads = 3;
adengage_layout_type = "horiz";
adengage_show_your_ad_here = 0;
adengage_yourlink_text = "";
adengage_show_powered_by = 0;
adengage_show_click_count = 1;
adengage_class_suffix = adengage_layout_type;
adengage_arrow_text = "";
window.adengage_draw_break = 1;
window.adengage_use_image = 1;
window.adengage_image_size = 100;
window.adengage_hide_desc = 0;
</SCRIPT><SCRIPT SRC="http://adcode.adengage.com/js/ae_12887_adbox.js" LANGUAGE="JavaScript"></SCRIPT>
<!-- END ADENGAGE.COM CODE 2.0 -->
</td>
<td> <a target = "_top" href="<?=$reportlink;?>"><img src="<?=$base_url;?>images/report.gif" alt="Report Invalid or SPAM Content" width="56" height="15" border="0" />
        </td>
      </tr>
    </table>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-504511-8";
urchinTracker();
</script>
</body>
</html>
