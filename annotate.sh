#!/bin/bash
callgrind_annotate \
	$(ls *.log | tail -n1) \
	"$@" \
	| less
