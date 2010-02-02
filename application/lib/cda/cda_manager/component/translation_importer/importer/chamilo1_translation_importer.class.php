<?php

class Chamilo1TranslationImporter extends TranslationImporter
{
	function scan_for_language_translations($file)
    {
    	$lang = array();
    	$fh = fopen($file, 'r');
    	
    	while (!feof($fh)) 
    	{
        	$line = fgets($fh);
			$line = trim($line);
			if(substr($line, 0, 1) == '$')
			{
				
				$pos = strpos($line, ' ');
				$variable = substr($line, 1, $pos - 1);
				
				$line = substr($line, $pos);
				$pos1 = strpos($line, '"');
				$translation = substr($line, $pos1 + 1, -2);
				
				$translations[$variable] = $translation;
				
			}
   	 	}
   	 	
    	fclose($fh);
    	
    	return $translations;
    }
}

?>