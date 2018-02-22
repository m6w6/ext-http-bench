<?php

return new unit(
	"Send response",
	25000,
	__FILE__,
	function($data) {
		$r = new http\Env\Response;
		$r->setBody(new http\Message\Body(fopen($data, "r")));
		$r->send(fopen("/dev/null", "w"));
	}
);
