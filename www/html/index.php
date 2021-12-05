<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php  echo gethostname(); ?></title>
	</head>
	<body>
		<h1><?php  echo gethostname(); ?></h1>
<?php
/* find latest scaled image. If it exists, put in page */

$latestResized='';

if ( is_dir('cam/latest') ) {
	$dir=opendir('cam/latest');

	while ( $f=readdir($dir) ) {
		$parts=explode('.',$f);

		if ( 3==count($parts) && 'latest' == $parts[0] && 'jpg' == $parts[2]  ) {
			$latestResized=$f;

			break;
		}
	}

	closedir($dir);
}

if ( '' != $latestResized ) {
	$parts=explode('.',$latestResized);
	$dims=explode('x',$parts[1]);

	$ctime=filectime('cam/latest/' . $latestResized);
	printf("<h2>Latest still image from %s. It is now %s</h2>\n",date("Y-m-d H:i:s",$ctime),date("Y-m-d H:i:s"));

	printf("<img src=\"/cam/latest/%s\" width=\"%s\" height=\"%s\" style=\"width: 50%%; height: auto;\" alt=\"latest captured photo\" />\n",$latestResized,$dims[0],$dims[1]);
	printf("<br />This image does not automatically refresh. Reload page to check for new image.");
}


?>
		<h2>Directory Listing</h2>
			<ul>
<?php
$dir=opendir('.');

$skipFiles=array('index.php','cgi-bin');

while ( $f=readdir($dir) ) {
	if ( '.' == substr($f,0,1) || in_array($f,$skipFiles) ) {
		continue;
	} else if ( 'cgi-bin' == $f ) {
		continue;
	}

	if ( is_dir($f) ) {
		$f = '/' . $f . '/';
	} else { 
		$f = '/' . $f;
	}

	printf("<li><a href=\"%s\">%s</a></li>\n",$f,$f);
}

closedir($dir);

?>
		</ul>

	</body>
</html>
