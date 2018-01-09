<?php

/*
script: upgrade.php
purpose: This file will update the phpLinkDropper Script from v1 to v2.
copyright: copyright 2006-2007 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/


session_start();
error_reporting(0);

include("config.php");

echo "<b>phpLinkDropper v1.0 to v2.0 UPGRADE FILE!</b><br/>";

echo "Adding new tables to current database.<br/>";

$sql='CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `itemid` int(11) NOT NULL,
  `userid` int(11) NOT NULL default \'0\',
  `comment` text  NOT NULL,
  `dateadded` int(11) NOT NULL,
  `ipaddress` tinytext  NOT NULL,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
);';
mysql_query($sql);

$sql='CREATE TABLE `domains` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain` mediumtext  NOT NULL,
  `title` text  NOT NULL,
  `userid` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  `hitsin` int(11) NOT NULL,
  `hitsout` int(11) NOT NULL,
  `dayin` int(11) NOT NULL,
  `dayout` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL default \'1\',
  PRIMARY KEY  (`id`)
);';
mysql_query($sql);

$sql='CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` mediumtext  NOT NULL,
  `maxday` int(11) NOT NULL default \'0\',
  `ratio` int(11) NOT NULL,
  `approval` tinyint(4) NOT NULL default \'0\',
  `minhits` int(11) NOT NULL default \'0\',
  `inpoints` int(11) NOT NULL default \'0\',
  `outpoints` int(11) NOT NULL default \'0\',
  `refpoints` int(11) NOT NULL default \'0\',
  `minpoints` int(11) NOT NULL default \'0\',
  `defaultg` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);';
mysql_query($sql);

$sql = "INSERT INTO `groups` VALUES (1, 'Visitor', 3, 33, 0, 20, 2, 1, 10, 300, 1, 1);";
mysql_query($sql);

$sql='CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` mediumtext  NOT NULL,
  `password` mediumtext  NOT NULL,
  `email` mediumtext  NOT NULL,
  `joindate` int(11) NOT NULL,
  `referredby` int(11) NOT NULL,
  `laston` int(11) NOT NULL,
  `confirmation` mediumtext  NOT NULL,
  `ugroup` int(11) NOT NULL default \'0\',
  `points` int(11) NOT NULL,
  `referrals` int(11) NOT NULL default \'0\',
  `preapproved` smallint(6) NOT NULL default \'0\',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);';
mysql_query($sql);

echo "Altering current tables.<br/>";

echo "Altering CATEGORIES table.<<br/>";
$sql = 'ALTER TABLE `categories` ADD `defaultimage` text NOT NULL;';
mysql_query($sql);

echo "Altering PLUGS table.<<br/>";
$sql = 'ALTER TABLE `plugs` ADD `description` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `plugs` ADD `maxclicks` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `plugs` ADD `impressions` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `plugs` ADD `maximp` int(11) NOT NULL;';
mysql_query($sql);

echo "Altering ITEMS table.<<br/>";
$sql = 'ALTER TABLE `items` ADD `userid` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `description` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `image` mediumtext NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `votes` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `points` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `yes` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `no` int(11) NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `items` ADD `publishdate` int(11) NOT NULL;';
mysql_query($sql);

echo "Altering SITECONFIG table.<br/>";
$sql = 'ALTER TABLE `siteconfig` ADD `defaultusername` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `membercomment` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `membersubmit` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `emailconfirmation` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `autochmod` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `maxthumbheight` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `maxthumbwidth` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `maxpublish` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `publishtime` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `contactemail` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `cachetime` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `memberlogin` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `maxcomments` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `requirethumb` smallint(6) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `skim` int(11) NOT NULL default \'0\';';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `skimurl` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `templatedirectory` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `defaultimage` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `siteconfig` ADD `lastreset` int(11) NOT NULL default \'0\';';
mysql_query($sql);

echo "Altering the USERLOG table.<br/>";
$sql = 'ALTER TABLE `userlog` ADD `username` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `userlog` ADD `refdomain` mediumtext NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `userlog` ADD `refpage` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `userlog` ADD `currentpage` text NOT NULL;';
mysql_query($sql);
$sql = 'ALTER TABLE `userlog` ADD `pageviews` int(11) NOT NULL default \'0\';';
mysql_query($sql);


$update_template = mysql_query("UPDATE siteconfig
set templatedirectory = 'v2Default',
defaultimage = 'defaultimage.gif'");

echo "UPGRADE COMPLETED!";

?>
