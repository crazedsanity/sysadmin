#!/bin/bash

if [ ! -r VERSION ]; then
	exit 1;

fi;

v=`grep "VERSION:" VERSION | cut -f2 -d " "`
p=`grep "PROJECT:" VERSION | cut -f2 -d " "`
#TAG="git tag v$v"
LASTTAG=`git describe --tags --abbrev=0`
tmpfile=''
EXTRALOGCMD=''
if [ -n "$LASTTAG" ]; then
	EXTRALOGCMD="${LASTTAG}..HEAD "
	tmpfile=$(mktemp /tmp/_tagAndPush.XXXXX)
	git log ${EXTRALOGCMD}--oneline > ${tmpfile}
fi
TAG="git tag v${v} -a -F ${tmpfile}"
PUSH="git push"
PUSH2="git push --tags"
CMD="$TAG && $PUSH && $PUSH2"

echo "Tagging ${p} as ${v}... ANNOTATION: "
echo "The following will be in the annotation: ";
git log ${EXTRALOGCMD}--oneline
echo -e "\nThe command: ";
echo -e "\t${CMD}"
read -p "Press [enter] to continue..."

$TAG && $PUSH && $PUSH2

rm -f $tmpfile
