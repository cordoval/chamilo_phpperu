<?php

class Chamilo1TranslationExporter extends TranslationExporter
{
	function export_language($directory, $language)
	{
		// For Chamilo 1.8.x some legacy names for language folders should be used.
		$language_english_name = str_replace(array('basque', 'turkish'), array('euskera', 'turkce'), $language->get_english_name());

		$lang_dir = $directory . $language_english_name . '/';
		if(!is_dir($lang_dir))
		{
			Filesystem :: create_dir($lang_dir);
		}

		foreach($this->get_language_packs() as $language_pack)
		{
			$this->export_language_pack($lang_dir, $language, $language_pack);
		}
	}

	function export_translation($handle, $language_pack_name, $translation)
	{
		$variable = $this->retrieve_variable_from_translation($translation);

		$trans = $translation->get_translation();
		if (!empty ($trans)) {
		  $trans = str_replace('"', '\\"', $trans);
		  fwrite($handle, '$' . $variable->get_variable() . ' = "' . $trans . "\";\n");
		}
	}

	function write_file_header($handle)
    {
    	fwrite($handle, "<?php\n/*\nfor more information: see languages.txt in the lang folder.\n*/\n");
    }
}

?>