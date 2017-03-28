#!/bin/bash
#
# This script configures WordPress file permissions based on recommendations
# from http://codex.wordpress.org/Hardening_WordPress#File_permissions
#
# Author: Michael Conigliaro <mike [at] conigliaro [dot] org>
#
WP_OWNER=apache # <-- wordpress owner
WP_GROUP=apache # <-- wordpress group
WP_ROOT=/home/changeme # <-- wordpress root directory
WS_GROUP=apache # <-- webserver group

## (Crazed)Sanity changes to take command line arguments
USAGE="Usage: $0 /path/to/wp-install-folder [owner=apache] [group=apache]"

if [ ! -z "$1" ]; then
	if [ -d "$1" ]; then
		WP_ROOT=$1
	else
		echo "Install directory does not exist ($1)"
		echo $USAGE
		exit 1;
	fi
else
	echo "passed directory test ($1)"
	echo "No install directory specified"
	echo $USAGE
	exit 1;
fi

if [ ! -z "$2" ]; then
	WP_OWNER="$2"
fi

if [ ! -z "$3" ]; then
	WP_GROUP="$3"
fi
WS_GROUP=$WP_GROUP

echo "SETTINGS::: WP_ROOT=(${WP_ROOT}), WP_OWNER=(${WP_OWNER}), WP_GROUP=(${WP_GROUP}), WS_GROUP=(${WS_GROUP})"
#exit 99;


# reset to safe defaults
echo "... resetting permissions to safe defaults..."
find ${WP_ROOT} -exec chown ${WP_OWNER}:${WP_GROUP} {} \;
find ${WP_ROOT} -type d -exec chmod 755 {} \;
find ${WP_ROOT} -type f -exec chmod 644 {} \;

# allow wordpress to manage wp-config.php (but prevent world access)
echo "... allowing wordpress to manage wp-config.php (but preventing world access)... "
chgrp ${WS_GROUP} ${WP_ROOT}/wp-config.php
chmod 660 ${WP_ROOT}/wp-config.php

# allow wordpress to manage .htaccess
echo "... allowing wordpress to manage .htaccess... "
touch ${WP_ROOT}/.htaccess
chgrp ${WS_GROUP} ${WP_ROOT}/.htaccess
chmod 664 ${WP_ROOT}/.htaccess

# allow wordpress to manage wp-content
echo "... allowing wordpress to manage wp-content ..."
find ${WP_ROOT}/wp-content -exec chgrp ${WS_GROUP} {} \;
find ${WP_ROOT}/wp-content -type d -exec chmod 775 {} \;
find ${WP_ROOT}/wp-content -type f -exec chmod 664 {} \;

echo "... DONE!"


