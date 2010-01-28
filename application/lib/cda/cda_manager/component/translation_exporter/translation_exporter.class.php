<?php
/**
 * $Id: translation_exporter.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 */
/**
 * Abstract class to export translations
 */
abstract class TranslationExporter
{
	private $languages;
	private $language_packs;
	private $user;
	
    /**
     * Constructor
     * @param Array languages - ids
     * @param Array language_packs - ids
     */
    public function TranslationExporter($user, $languages, $language_packs)
    {
    	$this->set_user($user);
    	$this->set_languages($languages);
    	$this->set_language_packs($language_packs);	
    }
    
    public static function factory($branch, $user, $languages, $language_packs)
    {
        $file = dirname(__FILE__) . '/exporter/chamilo' . $branch . '_translation_exporter.class.php';
        $class = 'Chamilo' . $branch . 'TranslationExporter';
        
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($user, $languages, $language_packs);
        }
        else
        {
        	throw new Exception(Translation :: get('TranslationExporterNotFound'));
        }
    }
    
    function get_languages()
    {
    	return $this->languages;
    }
    
    function set_languages($languages)
    {
    	$this->languages = $languages;
    }
    
	function get_language_packs()
    {
    	return $this->language_packs;
    }
    
    function set_language_packs($language_packs)
    {
    	$this->language_packs = $language_packs;
    }
    
	function get_user()
    {
    	return $this->user;
    }
    
    function set_user($user)
    {
    	$this->user = $user;
    }
    
	function export_translations()
	{
		$directory = Path :: get(SYS_TEMP_PATH) . $this->get_user()->get_id() . '/translations/';
		
		if(!is_dir($directory))
		{
			Filesystem :: create_dir($directory);
		}
		
		foreach($this->get_languages() as $language)
		{
			$this->export_language($directory, $language);
		}
		
		$zip = Filecompression :: factory();
		$zip->set_filename('translations.zip');
		$new_path = $zip->create_archive($directory);
		
		Filesystem :: remove($directory);
		
		$zip_path = Path :: get(SYS_TEMP_PATH) . $this->get_user()->get_id() . '/translations.zip';
		Filesystem :: remove($zip_path);
		rename($new_path, $zip_path);  
		
		return $this->make_web_path($zip_path);
	}
	
	function export_language($directory, $language)
	{
		$lang_dir = $directory . $language->get_english_name() . '/';
		if(!is_dir($lang_dir))
		{
			Filesystem :: create_dir($lang_dir);
		}
		
		foreach($this->get_language_packs() as $language_pack)
		{
			$this->export_language_pack($lang_dir, $language, $language_pack);
		}
	}
	
	function export_language_pack($lang_dir, $language, $language_pack)
	{
		$file = $lang_dir . $language_pack->get_name() . '.inc.php';
		$handle = fopen($file, 'w');
		
		$this->write_file_header($handle);
		
		$translations = $this->get_translations($language, $language_pack);
		while($translation = $translations->next_result())
		{
			$this->export_translation($handle, $language_pack->get_name(), $translation);
		}
		
		$this->write_file_footer($handle);
		
		fclose($handle);
	}
    
 	function write_file_header($handle)
    {
    	fwrite($handle, "<?php\n");
    }
    
	function write_file_footer($handle)
	{
		fwrite($handle, "?>");
	}
    
    function get_translations($language, $language_pack)
    {
    	$subselect_condition =  new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
    	$conditions[] = new SubSelectcondition(VariableTranslation :: PROPERTY_VARIABLE_ID, 
    					Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subselect_condition);
    	$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
    	$condition = new AndCondition($conditions);
    	
    	return CdaDataManager :: get_instance()->retrieve_variable_translations($condition);
    }
    
    function retrieve_variable_from_translation($translation)
    {
    	return CdaDataManager :: get_instance()->retrieve_variable($translation->get_variable_id());
    }
    
    function make_web_path($sys_path)
    {
    	return str_replace(Path :: get(SYS_PATH), Path :: get(WEB_PATH), $sys_path);
    }
    
    abstract function export_translation($handle, $language_pack_name, $translation);
    
}
?>