<?php

class ActionStorage
{
	private $actions; 
		
	public function ActionStorage($controller)
	{
		$this->actions = array();	
	}
	
	
	public function add_action($action)
	{
		if(!is_null($action))
		{
			array_push($this->actions, $action);
		}
	}
	
	/*
	 * Delete an actions from the actionstorage
	 */
	public function delete_action($action)
	{
		if(!is_null($actions))
		{
			for($i=0; $i<count($this->actions);$i++)
			{
				if($this->actions[$i]===$actions)
    				unset($this->actions[$i]);
			}	
		}	
	}
	
	/*
	 * Retrieve a certain actions from the actionstorage
	 */
	public function retrieve_action($action)
	{
		$object = null;
		if(!is_null($action))
		{
			foreach ($this->actions as $value) 
			{
    			if($value->get_name() == $action)
    				$object = $value;
			}		
		}		
		return $object;
	}
	
	/*
	 * Get the array of the actions that were addded to the form
	 */
	public function get_actions()
	{
		return $this->actions;
	}
}
?>