#!/bin/bash

## allow length specification
LENGTH="$1"
if [ -z "$1" ]; then
	LENGTH=12
fi;

## Generate by pulling random characters.
PASS=`< /dev/urandom tr -dc "_A-Z-a-z-0-9#.@%=+*" | head -c${LENGTH}`

## don't emit a newline (\n), that might mess other things that take this as an input
echo -n "$PASS"
