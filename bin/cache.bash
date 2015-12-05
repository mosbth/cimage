#!/bin/bash
#
# ls -ult list and sorts fils by its access time
#
#
# Main, start by checking basic usage
#
if [ $# -lt 1 ]
then
    echo "Usage: $0 [cache-dir]"
    exit 1
elif [ ! -d "$1" ]; then
    echo "Usage: $0 [cache-dir]"
    echo "$1 is not a directory."
    exit 1
fi



#
# Print out details on cache-directory
#
echo "# Size"
echo "Total size:       $( du -sh $1 | cut -f1 )"
echo "Number of files:  $( find $1 -type f | wc -l )"
echo "Number of dirs:   $( find $1 -type d | wc -l )"
echo
echo "# Top-5 largest files/dirs:"
echo "$( du -s $1/* | sort -nr | head -5 )"
echo 
echo "# Last-5 created files:"
echo "$( find $1 -type f -printf '%TY-%Tm-%Td %TH:%TM %p\n' | sort -r | head -5 )"
echo
echo "# Last-5 accessed files:"
echo "$( find $1 -type f -printf '%AY-%Am-%Ad %AH:%AM %f\n' | sort -r | head -5 )"
echo
echo "# 5 Oldest files:"
echo "$( find $1 -type f -printf '%AY-%Am-%Ad %AH:%AM %f\n' | sort | head -5 )"
echo
echo "# Files not accessed within the last 30 days"
echo "Number of files: $( find $1 -type f -atime +30 | wc -l )"
echo "Total file size: $( find $1 -type f -atime +30 -exec du {} \; | cut -f1 | paste -sd+ | bc )"
echo
