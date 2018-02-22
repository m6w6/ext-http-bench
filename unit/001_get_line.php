<?php

return new unit(
	"Parsing \"GET / HTTP/1.1\"",
	400000,
	"GET / HTTP/1.1",
	function($data) {
		new http\Message($data);
	}
);
