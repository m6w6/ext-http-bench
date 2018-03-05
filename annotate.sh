#!/bin/bash
callgrind_annotate \
	$(ls logs/cg_*.log | tail -n1) \
	"$@" \
	| less
