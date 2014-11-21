#!/bin/bash

#
# Specify the utilities used
#
ECHO="printf"



#
# Main, start by checking basic usage
#
if [ $# -lt 1 ]
then
    $ECHO "Usage: $0 [cache-dir]\n"
    exit 1
elif [ ! -d "$1" ]; then
    $ECHO "Usage: $0 [cache-dir]\n"
    $ECHO "$1 is not a directory.\n"
    exit 1
fi



#
# Print out details on cache-directory
#
$ECHO "Total size:       $( du -sh $1 | cut -f1 )"
$ECHO "\nNumber of files:  $( find $1 | wc -l )"
$ECHO "\n\nTop-5 largest files:\n"
$ECHO "$( du -s $1/* | sort -nr | head -5 )"
$ECHO "\n\nLast-5 created files:\n"
$ECHO "$( find $1/* -printf '%TY-%Tm-%Td %TH:%TM %p\n' | sort -r | head -5 )"
$ECHO "\n\nLast-5 accessed files:\n"
$ECHO "$( find $1/* -printf '%AY-%Am-%Ad %AH:%AM %f\n' | sort -r | head -5 )"
$ECHO "\n"
