<?php

return new unit(
	"Encode/Decode brotli stream",
	15000,
	file(__FILE__),
	function($data) {
		$enc = new http\Encoding\Stream\Enbrotli;
		$dec = new http\Encoding\Stream\Debrotli;
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
