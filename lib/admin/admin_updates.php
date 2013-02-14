<?php
//  WEBSITE ADMINISTRATION PROGRAM
//  Developed by Michael V. Petrovich, 2006

$REV = '20080411';

/*
20080411: Rewrote drag routines
               added &amp; to edit menu
20080301: added syntax print version for content
20070327: Added multiple image folders to upload images, fixed archive
20070316:  Added Multiple Document directories using the array $DocLinkDirs, and added rename/delete to document listing
           Deleted Preview Documents
20070224:  Restyled login pages.  Added File Date Checking, and Error Messages
20070109:  added image view after upload, added preview function
20061230:  added flags for title and content modified
20061217:  split into multiple files, added link view
20061205:  added T option to view template
20061203:  added find and replace
20061202:  added getupdates, main menu with dropdown
20061125:  added drop down menu items
20061121:  use GetDirectory from MVPtools;
           added image file size;
20061120:  1px border used on buttons was 2px
20061115:  Use $PHP_SELF to find Admin files ($SCRIPT_FILENAME does not work with CGI PHP);
           added Upload Document
20061105:  Fixed abilty to use in IE; 
           use $SCRIPT_FILENAME for admin file location
20061030:  added File Manager
20061027:  added $REV
20061024:  require_once for mvptools was $SITE_ROOT changed to $DOCUMENT_ROOT
           added $_POST and $_GET variables
20060915:  added <h1> filename text when created new files
           added chmod(766) to new files
20060920:  added view all images in subdirectories
*/

?>