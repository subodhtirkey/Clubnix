<?php
class autoActiveLink {

    function makeActiveLink($originalString){
	
        $newString = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" target=\"_blank\">\\0</a>", $originalString);
        return $newString;
    }

}
?>