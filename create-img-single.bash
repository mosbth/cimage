#!/bin/bash

#
# Paths and settings
#
TARGET="webroot/img_single.php"
NEWLINES="\n\n\n"



#
# Specify the utilities used
#
ECHO="printf"



#
# Main, start by checking basic usage
#
if [ $# -gt 0 ]
then
    $ECHO "Usage: $0\n"
    exit 1
fi



#
# Print out details on cache-directory
#
$ECHO "Creating webroot/img_single.php by combining the following files:"
$ECHO "\n"
$ECHO "\n webroot/img_single_header.php"
$ECHO "\n CHttpGet.php"
$ECHO "\n CRemoteImage.php"
$ECHO "\n CImage.php"
$ECHO "\n webroot/img.php"
$ECHO "\n"

$ECHO "\nPress enter to continue. "
read answer


#
# Create the $TARGET file
#
cat webroot/img_single_header.php > $TARGET
$ECHO "$NEWLINES" >> $TARGET

tail -n +2 CHttpGet.php >> $TARGET
$ECHO "$NEWLINES" >> $TARGET

tail -n +2 CRemoteImage.php >> $TARGET
$ECHO "$NEWLINES" >> $TARGET

tail -n +2 CImage.php >> $TARGET
$ECHO "$NEWLINES" >> $TARGET

tail -n +2 webroot/img.php >> $TARGET
$ECHO "$NEWLINES" >> $TARGET

$ECHO "\nDone."
$ECHO "\n"
$ECHO "\n"
