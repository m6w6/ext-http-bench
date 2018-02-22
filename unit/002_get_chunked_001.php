<?php

return new unit(
	"Parsing simple GET messages with small chunked response", 
	100000,
	file_get_contents(__DIR__."/../data/get-rr-chunked.txt"),
	function($data) {
		new \http\Message($data);
	}
);
