#!/bin/bash

#
# Paths and settings
#
TARGET_D="webroot/imgd.php"
TARGET_P="webroot/imgp.php"
TARGET_S="webroot/imgs.php"
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
$ECHO "Creating '$TARGET_D', '$TARGET_P' and '$TARGET_S' by combining the following files:"
$ECHO "\n"
$ECHO "\n webroot/img_header.php"
$ECHO "\n defines.php"
$ECHO "\n functions.php"
$ECHO "\n CHttpGet.php"
$ECHO "\n CRemoteImage.php"
$ECHO "\n CWhitelist.php"
$ECHO "\n CAsciiArt.php"
$ECHO "\n CImage.php"
$ECHO "\n CCache.php"
$ECHO "\n CFastTrackCache.php"
$ECHO "\n webroot/img.php"
$ECHO "\n"
$ECHO "\n'$TARGET_D' is for development mode."
$ECHO "\n'$TARGET_P' is for production mode (default mode)."
$ECHO "\n'$TARGET_S' is for strict mode."
$ECHO "\n"

$ECHO "\nPress enter to continue. "
read answer


#
# Create the $TARGET_? files
#
cat webroot/img_header.php > $TARGET_P
cat webroot/img_header.php | sed "s|//'mode'         => 'production',|'mode'         => 'development',|" > $TARGET_D
cat webroot/img_header.php | sed "s|//'mode'         => 'production',|'mode'         => 'strict',|" > $TARGET_S

$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 defines.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 functions.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CHttpGet.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CRemoteImage.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CWhitelist.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CAsciiArt.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CImage.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CCache.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 CFastTrackCache.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

tail -n +2 webroot/img.php | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null
$ECHO "$NEWLINES" | tee -a $TARGET_D $TARGET_P $TARGET_S > /dev/null

php -w $TARGET_S > tmp && mv tmp $TARGET_S

$ECHO "\nDone."
$ECHO "\n"
$ECHO "\n"
