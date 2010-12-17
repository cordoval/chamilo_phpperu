<?php
class RuleStorage
{
	protected $rules;	
	
	public function RuleStorage()
	{
		$this->rules = array();		
	}
	
	/*
	 * Function to add a rule
	 */
	public function add_rule($rule)
	{
		if(!empty($rule))
		{	array_push($this->rules, $rule); }
	}

	/*
	 * Getter for the rule(s)
	 */
	public function get_rules()
	{
		return $this->rules;
	}
	
	/*
	 * Function to remove a rule 
	 */
	public function delete_rule($rule)
	{
		if(!empty($rule))
		{	
			for($i=0; $i<count($this->rules);$i++)
			{
				if($this->rules[$i]===$rule)
    				unset($this->rules[$i]);
			}					
		}
	}
	
	public function get_javascript()
	{
		$script = array();
		foreach($this->rules as $rule)
		{
			$script[] = $rule->get_script();
		}		
		return implode($script);
	}
}
?>