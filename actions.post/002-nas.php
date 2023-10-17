#!/usr/bin/php -q
<?
/*
Example script for storing an image on a samba drive
*/
define("SHARE_NAME","\\\\192.168.30.4\\photo");
define("SHARE_USERNAME","aprs");
define("SHARE_PASSWORD","");
define("SHARE_DIRECTORY","webcam/turkey");

if ( 4 != $_SERVER['argc'] ) {
	die("arguments: timestamp fullsizeFilename scaledFilename\n");
}

$timestamp=$_SERVER['argv'][1];
$full=$_SERVER['argv'][2];
$scaled=$_SERVER['argv'][3];

printf("############ %s ############\n",$_SERVER['argv'][0]);

$dir_year=date("Y",$timestamp);
$dir_year_month=date("Y/m",$timestamp);
$dir_year_month_day=date("Y/m/d",$timestamp);

$output_filename=date("Ymd_His",$timestamp) . ".jpg";

/* create directories (which may exist) and then put latest file */
$cmd_smbclient = sprintf("mkdir %s ; mkdir %s ; mkdir %s ; put %s %s/%s",
	$dir_year,
	$dir_year_month,
	$dir_year_month_day,
	$full,
	$dir_year_month_day,
	$output_filename
);	

//printf("smbclient commands: %s\n",$cmd_smbclient);

$cmd=sprintf("smbclient '%s' %s -D '%s' -U %s -c '%s'",
	SHARE_NAME,
	SHARE_PASSWORD,
	SHARE_DIRECTORY,
	SHARE_USERNAME,
	$cmd_smbclient
);

printf("Executing: %s\n",$cmd);	
passthru($cmd);

?>
