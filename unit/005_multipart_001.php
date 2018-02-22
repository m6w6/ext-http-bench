<?php
 
return new unit(
	"Parsing multipart/form-data messages and split their bodies of 2 parts",
	50000,
	file_get_contents(__DIR__."/../data/put-multipart-formdata.txt"),
	function($data) {
		(new \http\Message($data))->splitMultipartBody();
	}
);
