#!/bin/bash

set -eo pipefail

if ! test -d php-src; then
	git clone git@git.php.net:php-src.git php-src
fi

cd php-src
git checkout PHP-7.2
git pull

for ext in http propro raphf; do
	if ! test -e ext/$ext; then
		ln -s ~/src/ng-$ext.git ext/$ext
	fi
done

./buildconf
if ! test -f Makefile; then
	CFLAGS="-O2 -pipe -g -march=native" \
	./configure -C \
		--disable-all \
		--enable-cgi \
		--enable-json \
		--enable-hash \
		--enable-iconv \
		--enable-raphf \
		--enable-propro \
		--with-http
fi

make -j9 || make

cd ..
log="$(date +%x_%X).log"
valgrind \
	--tool=callgrind \
	--dump-instr=yes \
	--branch-sim=yes \
	--callgrind-out-file=cg_$log \
	 php-src/sapi/cli/php bench.php 0.1 \
		2>&1 | tee bb_$log
