<?php

/***
 * script to lock-down KKCMS sites.
 * TODO: stuff.
#*/


$folder = null;
$PATH = null;
//print_r($argv);
//print_r($_SERVER);
echo "\n";

$USAGE = "USAGE: ". $argv[0] ." /path/to/html/site.com [user [group [writabledir1 [writable dir2]]]]\n";
$USER = "apache";
$GROUP = "kkbold_web";

$IMG = array();

if($_SERVER['USERNAME'] !== 'root') {
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
	
//	for($i=0; $i <= 11; $i++) {
	foreach($argv as $i=>$val) {
		
		switch($i) {
			// script name.  Nothing to see here.
			case 0:
				break;
			
			// folder
			case 1:
				$folder = $val;
				if(is_dir($folder)) {
					$PATH = $folder;
				}
				elseif(is_dir('/var/www/html/'. $folder)) {
					$PATH = '/var/www/html/'. $folder;
				}
				else {
					echo "Fatal: invalid folder.\n";
					echo $USAGE;
					exit(1);
				}
				echo "# NOTE::: path is  (". $PATH .")\n";
				break;
			
			// USER
			case 2:
				$USER = $argv[$i];
				break;
			
			// GROUP
			case 3:
				$GROUP = $argv[$i];
				break;
			
			// writable directories (no PHP access)
			case 4:
			case 5:
			case 6:
			case 7:
				$tryPath = $PATH ."/". $argv[$i];
				if(is_dir($tryPath)) {
					$IMG[$i] = $tryPath;
					echo "# NOTE::: added image folder (write access, no php):: ". $tryPath ."\n";
				}
				else {
					echo "Fatal: folder does not exist (". $tryPath .")\n";
					echo $USAGE;
					exit(1);
				}
				break;
			
			default:
				echo "Fatal: too many arguments (". $i ."). Maybe you need to quote a folder name somewhere?\n";
				print_r($argv);
				echo $USAGE;
				exit(1);
		}
		
		if($i >= 11) {
			echo "# Fatal, too many arguments \n";
			echo $USAGE;
			exit;
		}
	}
	echo "# NOTE::: user is  (". $USER .")\n";
	echo "# NOTE::: group is (". $GROUP .")\n";
}



$commands = array(
	"chown {$USER}:{$GROUP} {$PATH} -R",
	"find $PATH -type d -exec chmod 570 {} +",
	"find $PATH -type f -exec chmod 460 {} +",	
);
foreach($IMG as $key=>$imgPath) {
	$theFile = $imgPath .'/.htaccess';
	$commands[] = 'echo "php_flag engine off" > '. $theFile;
	$commands[] = 'chmod u+w '. $theFile .' -R';
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
	
//	exit(0);
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


