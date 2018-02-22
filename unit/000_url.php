<?php

return new unit(
	"Parse normal URL",
	500000,
	"https://www.google.com/search?q=list+of+valid+urls&client=firefox-b-ab&dcr=0&ei=aTdSOWu_3HM7TsAf__Y2gDw&start=20&sa=N&biw=917&bih=1047#bottom",
	function ($url) {
		new http\Url($url);
	}
);
