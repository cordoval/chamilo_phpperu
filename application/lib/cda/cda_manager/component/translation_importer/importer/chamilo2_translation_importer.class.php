<?php

class Chamilo2TranslationImporter extends TranslationImporter
{
	function scan_for_language_translations($file)
    {
    	$lang = array();
    	require_once($file);
    	
    	$lp = substr($file, 0, -8);
    	
    	return $lang[$lp];
    }
}

?>