#!/usr/bin/env php
<?php


/***
 * script to lock-down KKCMS sites.
 * TODO: make sure there's a rhyme and reason to how things are echo'ed
#*/


$folder = null;
$PATH = null;
//print_r($argv);
//print_r($_SERVER);
echo "\n";

$USAGE = "USAGE: ". $argv[0] ." /path/to/html/site.com/public_html/data [user [group]]\n";
$USAGE .= "# NOTE: I understand 'public_html' **and** 'wordpress' paths\n";
$USER = "apache";
$GROUP = "kkbold_web";

$IMG = array();


$whoIAm = null;
if(isset($_SERVER['USERNAME'])) {
	$whoIAm = $_SERVER['USERNAME'];
}
elseif(isset($_SERVER['USER'])) {
	$whoIAm = $_SERVER['USER'];
}

if($whoIAm !== 'root') {
	echo "# ---->>> !!!!  WITH GREAT POWER COMES GREAT RESPONSIBILITY\n".
	"# You must have root access to run this script.\n";
	echo $USAGE;
	exit(1);
}

// Test arguments.
if(count($argv) < 2) {
	echo "# Not enough arguments.\n";
	echo $USAGE;
	exit(1);
}
else {
	
	
	/* 
	 * allow for smart handling of CLI args.  Because this is a smart script.
	 */
	
	if(preg_match('~public_html/~', $argv[1]) == 1 || preg_match('~wordpress/~', $argv[1]) == 1) {
		// smart user!
		$separator = 'public_html/';
		$bits = explode($separator, $argv[1]);
		if(count($bits) !== 2) {
			$separator = 'wordpress/';
			$bits = explode($separator, $argv[1]);
		}
		if(count($bits) !== 2) {
			echo "Fatal: something went wrong, lost my (crazed)sanity\n";
			echo $USAGE;
			exit(1);
		}
		$folder = trim(preg_replace('~/{2,}~', '', $bits[0]));
		if(is_dir($folder)) {
			$PATH = $folder;
		}
		elseif(is_dir('/var/www/html/'. $folder)) {
			$PATH = '/var/www/html/'. $folder;
		}
		else {
			echo "Fatal: invalid folder (". $folder .")\n";
			echo $USAGE;
			exit(1);
		}
		$PATH = preg_replace('~/$~', '', $PATH);
		
		$myImgFolder = trim($bits[1]);
		$tryPath = $PATH .'/'. $separator . preg_replace('~/{1,}$~', '', $myImgFolder);
		if(is_dir($tryPath)) {
			$IMG[] = $tryPath;
			echo "# NOTE::: added image folder (write access, no php):: ". $tryPath ."\n";
		}
		else {
			echo "Fatal (". __LINE__ ."): folder does not exist (". $tryPath .")\n";
			echo $USAGE;
			exit(1);
		}
	}
	else {
		// TODO: allow specifying image directory as another argument (somewhere)
		echo "Fatal: too many options damages my (crazed)sanity\n";
		echo $USAGE;
		exit(1);
//		echo "# not too smart, but whatever.\n";
//		$folder = trim(preg_replace('~/~', '', $argv[1]));
	}

	// next argument is the user
	if(isset($argv[2]) && !empty($argv[2])) {
		// TODO: test to make sure this looks like a plain old user string...
		$USER = trim($argv[2]);
	}
	
	// next argument is the group.
	if(isset($argv[3]) && !empty($argv[3])) {
		// TODO: test to make sure this looks like a plain old group string...
		$GROUP = trim($argv[3]);
	}
	
}


echo "# NOTE::: user is  (". $USER .")\n";
echo "# NOTE::: group is (". $GROUP .")\n";


$commands = array(
	"chown {$USER}:{$GROUP} {$PATH} -R",
	"find $PATH -type d -exec chmod 570 {} +",
	"find $PATH -type f -exec chmod 460 {} +",	
);
foreach($IMG as $key=>$imgPath) {
	$theFile = $imgPath .'/.htaccess';
	
	// construct a multi-line HTACCESS file.
	/*
	<FilesMatch "\.(php|php\.)$">
	Order Allow,Deny
	Deny from all
	</FilesMatch>
	 */
	$commands[] = 'printf "php_flag engine off\n<FilesMatch \"\.(php|php\.)\$\">\nOrder Allow,Deny\n</FilesMatch>\n" > '. $theFile;
	
	
	$commands[] = 'chmod u+w '. $imgPath .' -R';
}
	
echo "# ---->>> !!!!  WITH GREAT POWER COMES GREAT RESPONSIBILITY\n";
echo "# Please review these commands before proceeding :\n";
print_r($commands);


// Wait for them to respond...
echo "# Press <Enter> to continue, or <Ctrl>+c to cancel...";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if($line == "\n" || trim(strtolower($line)) == 'yes') {
	echo "### Okay, here we go.  Look for errors.\n\n";
	
	foreach($commands as $i=>$cmd) {
		echo "## command #". $i ."::: ";
		passthru($cmd);
		echo "\n";
	}
}
else {
	echo "aborted\n";
}


exit(0);


