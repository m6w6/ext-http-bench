<?php

return new unit(
	"Encode/Decode gzip stream",
	15000,
	file(__FILE__),
	function($data) {
		$enc = new http\Encoding\Stream\Deflate(http\Encoding\Stream\Deflate::TYPE_GZIP);
		$dec = new http\Encoding\Stream\Inflate;
		foreach ($data as $line) {
			$line = $enc->update($line);
			if (strlen($line)) {
				$dec->update($line);
			}
		}
		$line = $enc->finish();
		if (strlen($line)) {
			$dec->update($line);
		}
		$dec->finish();
	}
);
