<?php

require_once dirname(__FILE__) . '/../../cda_data_manager.class.php';

class VariableScanner
{
	private $language_pack;
	private $log_handle;
	
	function set_language_pack($language_pack)
	{
		$this->language_pack = $language_pack;
	}
	
	function get_language_pack()
	{
		return $this->language_pack;
	}
	
	function scan_language_pack($root, $language_pack_name, $language_pack_type)
	{
		$this->set_language_pack($this->retrieve_language_pack($language_pack_name, $language_pack_type));
		
		if($this->log_handle)
		{
			fclose($this->log_handle);
			$this->log_handle = null;
		}
		
		$this->log_handle = fopen(dirname(__FILE__) . '/logs/' . $language_pack_name . '.log', 'w');
		
		$this->scan_directory($root);
	}
	
	private function scan_directory($root)
	{
		$files = Filesystem :: get_directory_content($root);
		$total_valid_variables = 0;
		$total_invalid_variables = 0;
		
		foreach($files as $file)
		{
			if(is_dir($file))
				continue;
			
			if(substr($file, -4) == '.php')
			{
				$this->log('Scanning file: ' . realpath($file));
				
				$contents = file_get_contents($file); 
				
				$valid_variables = $this->scan_for_valid_variables($contents);
				$total_valid_variables += $valid_variables;
				$invalid_variables = $this->scan_for_invalid_variables($contents);
		        $total_invalid_variables += $invalid_variables;
				
				$count = $valid_variables + $invalid_variables;
				
		        if($count == 0)
		        {
		        	$this->log('No variables found');
		        }
			}
		}
		
		$this->log('Total valid variables: ' . $total_valid_variables);
		$this->log('Total invalid variables: ' . $total_invalid_variables);
	}
	
	private function scan_for_valid_variables($contents)
	{
		$pattern = '/Translation :: get\(([\'"])(\w+)([\'"])\)/i';
		$matches = array();
        preg_match_all($pattern, $contents, $matches);
        
        foreach($matches[2] as $match)
        {
        	$this->log('Variable found: ' . $match);
        	$this->handle_variable($match);
        }
        
        return count($matches[2]);
	}
	
	private function scan_for_invalid_variables($contents)
	{
		$pattern2 = '/Translation :: get\((\$?\w+)\)/i';
				
        $matches = array();
        preg_match_all($pattern2, $contents, $matches);
        
		foreach($matches[1] as $match)
        {
        	$this->log('[WARNING] Dynamic variable found: ' . $match);
        }
        
        return count($matches[1]);
	}
	
	private function handle_variable($variable_name)
	{
		$variable = $this->retrieve_variable($variable_name, $this->language_pack->get_id());
		if($variable)
			return;
		
		$same_variable_in_other_lp = $this->retrieve_variable($variable_name);
		if($same_variable_in_other_lp)
		{
			$language_pack = $this->retrieve_language_pack('common', LanguagePack :: TYPE_CORE);
			if($same_variable_in_other_lp->get_language_pack_id() != $language_pack->get_id())
			{
				$same_variable_in_other_lp->set_language_pack_id($language_pack->get_id());
				$same_variable_in_other_lp->update();
			
			}
			
			$this->log('[WARNING] Double variable found and moved to common: ' . $variable_name);
		}
		else
		{
			$variable = new Variable();
			$variable->set_language_pack_id($this->language_pack->get_id());
			$variable->set_variable($variable_name);
			$variable->create();
		}
	}
	
	private function retrieve_variable($variable_name, $language_pack_id)
	{
		$dm = CdaDataManager :: get_instance();
		
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_VARIABLE, $variable_name);
		if($language_pack_id)
		{
			$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
		}
		else
		{
			$subcondition = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, 2);
			$conditions[] = new SubselectCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, LanguagePack :: PROPERTY_ID, 
							'cda_' . LanguagePack :: get_table_name(), $subcondition);
		}
		
		$condition = new AndCondition($conditions);
		
		return $dm->retrieve_variables($condition)->next_result();
	}
	
	private function retrieve_language_pack($name, $type)
	{
		$dm = CdaDataManager :: get_instance();
		$conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_NAME, $name);
		$conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, 2);
		$condition = new Andcondition($conditions);
		$language_pack = $dm->retrieve_language_packs($condition)->next_result();
		if(!$language_pack)
		{
			$language_pack = new LanguagePack;
			$language_pack->set_branch(2);
			$language_pack->set_name($name);
			$language_pack->set_type($type);
			$language_pack->create();
		}
		
		return $language_pack;
	}
	
	private function log($message)
	{
		fwrite($this->log_handle, date('[H:i] ') . $message . "\n");
	}
}

?>