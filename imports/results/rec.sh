#!/bin/csh
rm /tmp/results.txt
foreach i (`ls $1/*/execution.out`)
	more $i | awk -f extract.awk >> /tmp/results.txt
    end;
more /tmp/results.txt | python import-csp.py
