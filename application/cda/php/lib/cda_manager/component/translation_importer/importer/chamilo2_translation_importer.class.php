<?php namespace application\cda;

class Chamilo2TranslationImporter extends TranslationImporter
{
	function scan_for_language_translations($file)
    {
    	$lang = array();
    	require_once($file);
    	
    	foreach($lang as $language_pack => $variables)
    	{
    		return $lang[$language_pack];
    	}
    }
}

?>