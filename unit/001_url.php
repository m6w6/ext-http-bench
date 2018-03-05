<?php

return new unit(
	"Parse unicode URL",
	500000,
	"http://example.com/uiopüpölokjhjklöäö?äölkop=püläöl",
	function ($url) {
		new http\Url($url, null, http\Url::PARSE_MBUTF8);
	}
);
