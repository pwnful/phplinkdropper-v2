<?
/*
script: main.php
purpose: This is the MAIN PAGE for your STAFF Contrl Panel.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

// If the user is not logged in as ADMIN, it will redirect them to login
if ($_SESSION['staffloggedin'] != '1')
staff_login_box();
// End REDIRECT
?>

<table width="98%"  border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td width="36%" valign="top" class="StatsTitle"><div align="right">Total Items in Database: </span></div></td>
    <td width="13%" valign="top" class="StatsText"><? echo count_items();?></span></td>
  <tr>
    <td valign="top" class="StatsTitle"><div align="right">Total Submissions Needing Confirmation: </div></td>
    <td valign="top" class="StatsText"><a href="index.php?action=managesubmissions"><? echo count_submissions();?></a></td>
  </tr>
  <tr>
    <td valign="top" class="StatsTitle"><div align="right">Total Reported Items: </div></td>
    <td valign="top" class="StatsText"><a href="index.php?action=manageitems&bt=reported"><? echo count_reported();?></a></td>
  </tr>
</table>
