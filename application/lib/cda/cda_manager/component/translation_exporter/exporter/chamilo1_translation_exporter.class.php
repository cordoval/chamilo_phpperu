<?php

class Chamilo1TranslationExporter extends TranslationExporter
{	
	function export_translation($handle, $language_pack_name, $translation)
	{
		$variable = $this->retrieve_variable_from_translation($translation);
		
		$trans = ($translation->get_translation == ' ') ? '' : $translation->get_translation();
		
		fwrite($handle, '$' . $variable->get_variable() . ' = "' . $trans . "\";\n");
	} 
	
	function write_file_header($handle)
    {
    	fwrite($handle, "<?php\n/*\nfor more information: see languages.txt in the lang folder.\n*/\n");
    }
}

?>