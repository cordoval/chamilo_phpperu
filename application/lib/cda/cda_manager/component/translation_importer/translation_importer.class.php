<?php
/*
 * @author Sven Vanpoucke
 */

abstract class TranslationImporter
{
	private $user;
	private $options;
	private $branch;
	
	const OPTION_CREATE_NEW_LANGUAGES = 1;
	const OPTION_CREATE_NEW_LANGUAGE_PACKS = 2;
	const OPTION_CREATE_NEW_VARIABLES = 3;
	
	/**
     * Constructor
     */
    public function TranslationImporter($branch, $user, $options = array())
    {
    	$this->set_branch($branch);
    	$this->set_user($user);
    	$this->set_options($options);
    	set_time_limit(0);
    }
    
	/**
	 * @return the $user
	 */
	public function get_user()
	{
		return $this->user;
	}

	/**
	 * @param $user the $user to set
	 */
	public function set_user($user)
	{
		$this->user = $user;
	}

	/**
	 * @return the $options
	 */
	public function get_options()
	{
		return $this->options;
	}

	/**
	 * @param $options the $options to set
	 */
	public function set_options($options)
	{
		$this->options = $options;
	}
	
	public function set_option($option, $value)
	{
		$this->options[$option] = $value;
	}
	
	public function get_option($option)
	{
		return $this->options[$option];
	}

	/**
	 * @return the $branch
	 */
	public function get_branch()
	{
		return $this->branch;
	}

	/**
	 * @param $branch the $branch to set
	 */
	public function set_branch($branch)
	{
		$this->branch = $branch;
	}

	public static function factory($branch, $user, $options = array())
    {
        $file = dirname(__FILE__) . '/importer/chamilo' . $branch . '_translation_importer.class.php';
        $class = 'Chamilo' . $branch . 'TranslationImporter';
        
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($branch, $user, $options);
        }
        else
        {
        	throw new Exception(Translation :: get('TranslationImporterNotFound'));
        }
    }
    
    public function import($file)
    {
    	$temp = Path :: get(SYS_TEMP_PATH) . '/' . $this->user->get_id() . '/';
    	
    	if(!is_dir($temp))
    	{
    		Filesystem :: create_dir($temp);
    	}
    	
    	$path = $temp . 'languages.zip';
    	
    	move_uploaded_file($file['tmp_name'], $path);
    	
    	$filecompression = Filecompression :: factory();
    	$root = $filecompression->extract_file($path);

    	$this->import_translations($root);
    	
    	Filesystem :: remove($root);
    	Filesystem :: remove($path);
    }
    
    private function import_translations($root)
    {
    	$languages = $this->scan_for_languages($root);
    	
    	foreach($languages as $language)
    	{
    		$system_language = $this->get_language($language);
    		if(!$system_language)
    		{
    			continue;
    		}
    		
    		$is_translator = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: VIEW_RIGHT, $system_language->get_id(), 'cda_language');
			$is_moderator = CdaRights :: is_allowed_in_languages_subtree(CdaRights :: EDIT_RIGHT, $system_language->get_id(), 'cda_language');
	    		
			if(!$is_translator && !$is_moderator)
			{
				continue;
			}
			
    		$language_directory = $root . '/' . $language;
    		$language_packs = $this->scan_for_language_packs($language_directory);
    		
    		foreach($language_packs as $language_pack)
    		{
    			$system_language_pack = $this->get_language_pack($language_pack);
    			if(!$system_language_pack)
    			{
    				continue;
    			}
    			
    			$file = $language_directory . '/' . $language_pack . '.inc.phps';
    			$translations = $this->scan_for_language_translations($file);
    			
    			foreach($translations as $variable => $translation)
    			{
    				$system_variable = $this->get_variable($system_language_pack, $variable);
    				if(!$system_variable)
    				{
    					continue;
    				}
    				
    				$system_translation = $this->get_translation($system_language, $system_variable);
    				
    				if($translation && trim($translation) != '' && is_object($system_translation))
    				{
    					$system_translation->set_translation($translation);
    					$system_translation->set_user_id($this->get_user()->get_id());
    					$system_translation->set_date(Utilities :: to_db_date(time()));
    					$system_translation->update();
    				}
    			}	
    		}
    	}
    }
    
    private function scan_for_languages($root)
    {
    	$directories = Filesystem :: get_directory_content($root, Filesystem :: LIST_DIRECTORIES, false);
    	foreach($directories as $directory)
    	{
    		if(substr($directory, 0, 1) == '.')
    			continue;
    			
    		$languages[] = $directory;
    	}
    	
    	return $languages;
    }
    
    private function scan_for_language_packs($language_directory)
    {
    	$files = Filesystem :: get_directory_content($language_directory, Filesystem :: LIST_FILES, false);
    	foreach($files as $file)
    	{
    		if(substr($file, -9) != '.inc.phps')
    			continue;
			
    		$language_packs[] = substr($file, 0, -9);
    	}
    	
    	return $language_packs;
    }
    
	abstract function scan_for_language_translations($file);    
    
	
	private function get_language($language_name)
	{
		$dm = CdaDataManager :: get_instance();
		$condition = new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, $language_name);
		
		$language = $dm->retrieve_cda_languages($condition)->next_result();
		if(!$language && $this->get_option(self :: OPTION_CREATE_NEW_LANGUAGES))
		{
			$language = new CdaLanguage();
			$language->set_english_name($language_name);
			$language->set_original_name($language_name);
			$language->set_rtl(0);
			$language->set_isocode(substr($language_name, 0, 2));
			$language->create();
		}
		
		return $language;
	}
	
	private function get_language_pack($language_pack_name)
	{
		$dm = CdaDataManager :: get_instance();
		
		$conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_NAME, $language_pack_name);
		$conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $this->get_branch());
		$condition = new AndCondition($conditions);
		
		$language_pack = $dm->retrieve_language_packs($condition)->next_result();
		if(!$language_pack && $this->get_option(self :: OPTION_CREATE_NEW_LANGUAGE_PACKS))
		{
			$language_pack = new LanguagePack();
			$language_pack->set_branch($this->get_branch());
			$language_pack->set_name($language_pack_name);
			$language_pack->set_type(LanguagePack :: TYPE_APPLICATION);
			$language_pack->create();
		}
		
		return $language_pack;
	}
	
	private function get_variable($language_pack, $variable_name)
	{
		$dm = CdaDataManager :: get_instance();
		
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_VARIABLE, $variable_name);
		$condition = new AndCondition($conditions);
		
		$variable = $dm->retrieve_variables($condition)->next_result();
		if(!$variable && $this->get_option(self :: OPTION_CREATE_NEW_VARIABLES))
		{
			$variable = new Variable();
			$variable->set_language_pack_id($language_pack->get_id());
			$variable->set_variable($variable_name);
			$variable->create();
		}
		
		return $variable;
	}
	
	private function get_translation($language, $variable)
	{
		$dm = CdaDataManager :: get_instance();
		
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable->get_id());
		$condition = new AndCondition($conditions);
		
		return $dm->retrieve_variable_translations($condition)->next_result();
	}
}