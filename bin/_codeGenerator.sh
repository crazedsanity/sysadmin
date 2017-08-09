#!/bin/bash

## allow length specification
LENGTH="$1"
if [ -z "$1" ]; then
	LENGTH=8
fi;

## Generate by pulling random characters.
PASS=`< /dev/urandom tr -dc "A-Z0-9*" | head -c${LENGTH}`

## Newline is okay, since this is a code that will likely be copy+pasted (not used directly)
echo "$PASS"
