<?
/*
script: edititem.php
purpose: This file handles the edit item form.
copyright: copyright 2006 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/

// If the user is not logged in as ADMIN, it will redirect them to login
  check_security(1);
// End REDIRECT


?>


<table width="98%"  border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td colspan="2"><span class="PageTitles">Edit Item </span></td>
  </tr><form name="form1" method="post" action="index.php?action=updateitem">
  <tr>
    <td class="FormText">
     Item Name:</td>
    <td><input name="itemname" type="text" id="itemname" value="<?=$itemname;?>" size="40"></td>
  </tr>
  <tr>
    <td class="FormText">Item URL: </td>
    <td><input name="itemurl" type="text" id="itemurl" value="<?=$itemurl;?>" size="40"></td>
  </tr>
  <tr>
    <td class="FormText">Item Category: </td>
    <td><select name="itemcategory" id="itemcategory">
      <? echo list_categories($itemcategory);?>
    </select></td>
  </tr>
  <tr>
    <td class="FormText">Status:</td>
    <td><select name="itemstatus" id="itemstatus">
      <option value="0" <? if ($itemstatus == 0) echo "selected";?>>Inactive</option>
      <option value="1" <? if ($itemstatus == 1) echo "selected";?>>Active</option>
      <option value="2" <? if ($itemstatus == 2) echo "selected";?>>REPORTED</option>
      <option value="3" <? if ($itemstatus == 3) echo "selected";?>>Uncofirmed</option>
    </select></td>
  </tr>
  <tr>
    <td><input name="updateid" type="hidden" id="updateid" value="<?=$id;?>"><input name="reported" type="hidden" id="reported" value="<?=$bt;?>"></td>
    <td><input type="submit" name="Submit" value="Update Item"></td>
  </tr></form>
</table>
