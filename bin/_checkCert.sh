#!/bin/bash
SERVERNAME=$1
USAGE="## USAGE: $0 domainname.com"
if [ -z "$SERVERNAME" ]; then
	echo $USAGE
	exit 1
else
	echo | openssl s_client -showcerts -servername p.typekit.net -connect ${SERVERNAME}:443 2>/dev/null | openssl x509 -inform pem -noout -text
fi
