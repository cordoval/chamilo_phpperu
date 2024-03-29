<?php

namespace application\cda;

class Chamilo2TranslationExporter extends TranslationExporter
{	
	function export_translation($handle, $language_pack_name, $translation)
	{
		$variable = $this->retrieve_variable_from_translation($translation);
		
		$trans = ($translation->get_translation == ' ') ? '' : $translation->get_translation();
		$trans = str_replace("'", "\\'", $trans);
		
		fwrite($handle, '$lang[\'' . $language_pack_name . '\'][\'' . $variable->get_variable() . '\'] = \'' . $trans . "';\n");
	} 
}

?>