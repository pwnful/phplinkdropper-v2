<?php
/*
script: install.php
purpose: This file will install the phpLinkDropper Script.
copyright: copyright 2006-2007 phpLinkDropper & Scott Lewis. All Rights Reserved. You may modify this file for use on your
					site running a licenced copy of phpLinkDropper.
					You must not distribute this file or derivations of it.
support: www.phplinkdropper.com

*/


session_start();
error_reporting(0);
include("config.php");

echo "Setting up the database tables!<BR>";


mysql_query ("CREATE TABLE `banned` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ipaddress` text  NOT NULL,
  `url` text  NOT NULL,
  `date` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` text NOT NULL,
  `icon` text NOT NULL,
  `status` int(11) NOT NULL default '0',
  `defaultimage` text NOT NULL,
  PRIMARY KEY  (`id`)
)");

mysql_query ("INSERT INTO `categories` VALUES (1, 'Funny Videos', 'videos.jpg', 1, '')");
mysql_query ("INSERT INTO `categories` VALUES (2, 'Games', 'games.jpg', 1, '')");
mysql_query ("INSERT INTO `categories` VALUES (3, 'Photos', 'pictures.jpg', 1, '')");
mysql_query ("INSERT INTO `categories` VALUES (4, 'Music Videos', 'music_videos.jpg', 1, '')");

mysql_query ("CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `itemid` int(11) NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `comment` text  NOT NULL,
  `dateadded` int(11) NOT NULL,
  `ipaddress` tinytext  NOT NULL,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
)");

mysql_query ("CREATE TABLE `domains` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain` mediumtext  NOT NULL,
  `title` text  NOT NULL,
  `userid` int(11) NOT NULL,
  `dateadded` int(11) NOT NULL,
  `hitsin` int(11) NOT NULL,
  `hitsout` int(11) NOT NULL,
  `dayin` int(11) NOT NULL,
  `dayout` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` mediumtext  NOT NULL,
  `maxday` int(11) NOT NULL default '0',
  `ratio` int(11) NOT NULL,
  `approval` tinyint(4) NOT NULL default '0',
  `minhits` int(11) NOT NULL default '0',
  `inpoints` int(11) NOT NULL default '0',
  `outpoints` int(11) NOT NULL default '0',
  `refpoints` int(11) NOT NULL default '0',
  `minpoints` int(11) NOT NULL default '0',
  `defaultg` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
)");


mysql_query ("INSERT INTO `groups` VALUES (1, 'Visitors', 1, 0, 1, 1, 1, 1, 12, 0, 0, 1)");

mysql_query ("CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `ipaddress` text NOT NULL,
  `date` text NOT NULL,
  `reports` int(11) NOT NULL default '0',
  `category` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `image` mediumtext NOT NULL,
  `votes` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `yes` int(11) NOT NULL,
  `no` int(11) NOT NULL,
  `publishdate` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `links` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` text  NOT NULL,
  `url` text  NOT NULL,
  `email` text  NOT NULL,
  `dayin` int(10) unsigned NOT NULL default '0',
  `totalin` int(10) unsigned NOT NULL default '0',
  `dayout` int(11) NOT NULL,
  `totalout` int(11) NOT NULL,
  `description` mediumtext  NOT NULL,
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `plugs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` text NOT NULL,
  `image` text NOT NULL,
  `url` text NOT NULL,
  `status` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `plugicon` int(11) NOT NULL default '0',
  `startdate` int(11) NOT NULL default '0',
  `enddate` int(11) NOT NULL default '0',
  `alttext` text NOT NULL,
  `categories` text NOT NULL,
  `realclicks` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `maxclicks` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `maximp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `searches` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `searchterm` text NOT NULL,
  `results` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `siteconfig` (
  `baseurl` text NOT NULL,
  `sitename` text NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `frameout` int(11) NOT NULL default '0',
  `submissions` int(11) NOT NULL default '0',
  `sefriendly` int(11) NOT NULL default '0',
  `trackclicks` int(11) NOT NULL default '0',
  `logusers` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `username` text NOT NULL,
  `password` text NOT NULL,
  `maxpersite` int(11) NOT NULL default '0',
  `maxresults` int(11) NOT NULL default '0',
  `bannedmessage` text NOT NULL,
  `delay` int(11) NOT NULL default '0',
  `prune` int(11) NOT NULL default '0',
  `sitestatus` int(11) NOT NULL default '1',
  `offlinemessage` text NOT NULL,
  `maxtextplugs` int(11) NOT NULL default '0',
  `maximageplugs` int(11) NOT NULL default '0',
  `linktrading` int(11) NOT NULL,
  `maxtoplinks` int(11) NOT NULL,
  `sorttoplinks` int(11) NOT NULL,
  `sortlinks` int(11) NOT NULL,
  `realurl` int(11) NOT NULL,
  `defaultusername` text NOT NULL,
  `membercomment` smallint(6) NOT NULL default '0',
  `membersubmit` smallint(6) NOT NULL default '0',
  `emailconfirmation` smallint(6) NOT NULL default '0',
  `autochmod` smallint(6) NOT NULL default '0',
  `maxthumbheight` int(11) NOT NULL,
  `maxthumbwidth` int(11) NOT NULL,
  `maxpublish` int(11) NOT NULL,
  `publishtime` int(11) NOT NULL,
  `contactemail` text NOT NULL,
  `cachetime` int(11) NOT NULL,
  `memberlogin` smallint(6) NOT NULL default '0',
  `maxcomments` int(11) NOT NULL,
  `requirethumb` smallint(6) NOT NULL default '0',
  `skim` int(11) NOT NULL default '0',
  `skimurl` text NOT NULL,
  `templatedirectory` text NOT NULL,
  `defaultimage` text NOT NULL,
  `lastreset` int(11) NOT NULL default '0'
)");


mysql_query ("INSERT INTO `siteconfig` VALUES ('http://www.PUTYOURDOMAINHERE.com', 'phpLinkDropper v2.0RC1', 'link drop,drop a link,link droppgin', 'Welcome to the largest library of Media content.', 1, 1, 1, 1, 1, 2, 'admin', '21232f297a57a5a743894a0e4a801fc3', 2, 30, 'You cannot access the website at this time.', 3, 0, 1, 'We are currently closed for maintenance and apologize for this inconviencence. Please check back shortly.', 6, 7, 1, 9, 2, 3, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, '', 300, 0, 0, 0, 0, '', 'v2Default', '', 1175490060)");


mysql_query ("CREATE TABLE `staff` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `security` text NOT NULL,
  `status` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `ipaddress` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)");


mysql_query ("CREATE TABLE `userlog` (
  `ipaddress` text NOT NULL,
  `userid` int(11) NOT NULL default '0',
  `usertime` text NOT NULL,
  `username` text NOT NULL,
  `refdomain` mediumtext NOT NULL,
  `refpage` text NOT NULL,
  `currentpage` text NOT NULL,
  `pageviews` int(11) NOT NULL
)");


mysql_query ("CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` mediumtext  NOT NULL,
  `password` mediumtext  NOT NULL,
  `email` mediumtext  NOT NULL,
  `joindate` int(11) NOT NULL,
  `referredby` int(11) NOT NULL,
  `laston` int(11) NOT NULL,
  `confirmation` mediumtext  NOT NULL,
  `ugroup` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL,
  `referrals` int(11) NOT NULL default '0',
  `preapproved` smallint(6) NOT NULL default '0',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
)");

echo "<BR>Installation Complete. Please delete this file!";
?>
