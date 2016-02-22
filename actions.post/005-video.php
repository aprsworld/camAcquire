#!/usr/bin/php -q
<?
/* encode as time lapse video? */
/* locations */
define('DIR_HOURS','/home/aprs/cam/hours');
define('FILE_LAST_DIRECTORY','lastDirectory.dat');

if ( 4 != $_SERVER['argc'] ) {
	die("arguments: timestamp fullsizeFilename scaledFilename\n");
}

$timestamp=$_SERVER['argv'][1];
$full=$_SERVER['argv'][2];
$scaled=$_SERVER['argv'][3];

printf("############ %s ############\n",$_SERVER['argv'][0]);
//printf("# timestamp=%s\n",$timestamp);
//printf("# full=%s\n",$full);
//printf("# scaled=%s\n",$scaled);

/* create DIR_HOURS if it doesn't exist */
if ( ! is_dir(DIR_HOURS) ) {
	printf("# creating DIR_HOURS %s\n",DIR_HOURS);
	if ( ! mkdir(DIR_HOURS,0777,true) ) {
		printf("# error creating DIR_HOURS. Aborting.\n");
		return 1;
	}
}

/* create directory for this hour if it doesn't exist */
$output_directory=date("Y/m/d/H",$timestamp);
$output_directory=DIR_HOURS . '/' . $output_directory;
if ( ! is_dir($output_directory) ) {
	printf("# creating %s\n",$output_directory);
	if ( ! mkdir($output_directory,0777,true) ) {
		printf("# error creating. Aborting.\n");
		return 1;
	}
}

printf("# output directory=%s\n",$output_directory);

/* scan output directory to find the last / next file name */
$images=array();
$dir=opendir($output_directory);
while ( ($file = readdir($dir) ) !== false ) {
	/* skip enteries that start with '.' */
	if ( '.' == substr($file,0,1) )
		continue;
	if ( '.jpg' != substr($file,strpos($file,'.')) ) 
		continue;

	/* add to scripts array */
	$images[]=basename($file);
}
closedir($dir);
/* sort directory */
sort($images);
/* find the next image number */
if ( count($images) >= 1 ) {
	$last_image_file=$images[count($images)-1];
	$last_image_number=substr($last_image_file,0,strpos($last_image_file,'.'));
//	printf("# last_image_number='%s'\n",$last_image_number);
	$next_image_file=sprintf("%04d.jpg",$last_image_number+1);
} else {
	$last_image_file='';
	$next_image_file='0000.jpg';
}
//printf("# last_image_file=%s\n",$last_image_file);
//printf("# next_image_file=%s\n",$next_image_file);


/* copy the scaled image to next_image_file */
$destination_file=$output_directory . '/' . $next_image_file;
//printf("# destination_file=%s (for scaled image)\n",$destination_file);
copy($scaled,$destination_file);


/* now see if we need to process a previous directory */
$last_directory_file=DIR_HOURS . '/' . FILE_LAST_DIRECTORY;
//printf("# last directory file=%s\n",$last_directory_file);
$ld='';
if ( file_exists($last_directory_file ) ) {
	$fp=fopen($last_directory_file,'r');
	$ld=trim(fread($fp,256));
	fclose($fp);
}
//printf("# last directory=%s\n",$ld);

/* we are on to a new directory ... so we generate video */
if ( $ld != $output_directory ) {
	printf("# new output directory is different from last output directory.\n");
	printf("# will generate video and update last directory file\n");

	$video_file="timelapse.mp4";

	chdir($ld);
	
	$cmd_video=sprintf("gst-launch-1.0 -e multifilesrc location=\"%%04d.jpg\" ! image/jpeg, framerate=25/2 ! decodebin ! video/x-raw, width=1280, height=720 ! progressreport name=progress ! omxh264enc target-bitrate=6000000 control-rate=variable ! video/x-h264, profile=high ! h264parse ! mp4mux ! filesink location=%s",$video_file);
	passthru($cmd_video);


	printf("# writing '%s' to %s\n",$output_directory,$last_directory_file);
	$fp=fopen($last_directory_file,'w');
	fwrite($fp,$output_directory);
	fclose($fp);
}

?>
