#!/usr/bin/php -q
<?
define("LOCAL_DIR","/home/aprs/photos");

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

$dir_full=LOCAL_DIR . "/" . $dir_year_month_day;
mkdir($dir_full,0777,true);
$file_full=$dir_full . "/" . $output_filename;
copy($full,$file_full);
?>
