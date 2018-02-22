<?php

return new unit(
	"Send response",
	50000,
	file_get_contents(__FILE__),
	function($data) {
		$r = new http\Env\Response;
		$r->getBody()->append($data);
		$r->send(fopen("/dev/null", "w"));
	}
);
