#!/bin/bash

if [ ! -r VERSION ]; then
	exit 1;

fi;

xv=`grep "VERSION:" VERSION | cut -f2 -d " "`
v="$(echo -e "${xv}" | sed -e 's/[[:space:]]*$//')"
echo "before ${v} after"
pv=`grep "PROJECT:" VERSION | cut -f2 -d " "`
p="$(echo -e "${pv}" | sed -e 's/[[:space:]]*$//')"
#TAG="git tag v$v"
LASTTAG=`git describe --tags --abbrev=0`
tmpfile=$(mktemp /tmp/_tagAndPush.XXXXX)
EXTRALOGCMD=''
if [ -n "$LASTTAG" ]; then
	EXTRALOGCMD="${LASTTAG}..HEAD "
	git log ${EXTRALOGCMD}--oneline > ${tmpfile}
else 
	git log --oneline > ${tmpfile}
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
