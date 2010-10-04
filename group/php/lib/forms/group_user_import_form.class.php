<?php
/**
 * $Id: $
 * @author vanpouckesven
 * @package 
 */

class GroupUserImportForm extends FormValidator
{
	private $doc;
    private $failed_elements;
    
	/**
     * Creates a new GroupUserImportForm 
     * Used to import group users from a file
     */
    function GroupUserImportForm($action)
    {
        parent :: __construct('group_user_import', 'post', $action);
        
        $this->failed_elements = array();
        $this->build_importing_form();
    }
    
 	function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        $allowed_upload_types = array('csv');
        $this->addRule('file', Translation :: get('OnlyCSVAllowed'), 'filetype', $allowed_upload_types);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
	function import_group_users()
    {
        $values = $this->exportValues();
        $group_users = Import :: csv_to_array($_FILES['file']['tmp_name']);
        
        $validated_groups = array();
        
        foreach($group_users as $group_user)
        {
        	if($validated_group = $this->validate_group_user($group_user))
        		$validated_groups[] = $validated_group; 
        	else
        		$this->failed_elements[] = Translation :: get('Invalid') . ': ' . implode(";", $group_user);
        }

        if(count($this->failed_elements) > 0)
        	return false;
        
        $this->process_group_users($validated_groups);
        
        if(count($this->failed_elements) > 0)
        	return false;
        
        return true;
    }
    
 	function validate_group_user($group_user)
    {
    	//1. Check if action is valid
    	$action = strtoupper($group_user['action']);
    	if($action != 'A' && $action != 'D')
    	{
    		return false;
    	}
    	
    	//2. Check if name & code is filled in
    	if(!$group_user['group'] || $group_user['group'] == '' || !$group_user['username'] || $group_user['username'] == '')
    	{ 
    		return false;
    	}
    	
    	$group_user['group'] = $this->retrieve_group($group_user['group']);
    	
    	//3. Check if group exists
    	if(!$group_user['group'])
    	{
    		return false;
    	}
    	
    	$group_user['username'] = $this->retrieve_user($group_user['username']);
    	
    	//4. Check if user exists
    	if(!$group_user['username'])
    	{
    		return false;
    	}
    	
    	$group_user['group_user'] = $this->retrieve_group_user($group_user['group']->get_id(), $group_user['username']->get_id());
    	
    	//5. Check if groupuser exist with delete and if it doesn't exist yet with create
    	if( ($action == 'A' && $group_user['group_user']) || ($action == 'D' && !$group_user['group_user']))
    	{
    		return false;
    	}
    	
    	return $group_user;
    }
    
    function retrieve_group($group_code)
    {
    	$condition = new EqualityCondition(Group :: PROPERTY_CODE, $group_code);
    	return GroupDataManager :: get_instance()->retrieve_groups($condition)->next_result();
    }
    
    function retrieve_user($username)
    {
    	return UserDataManager :: get_instance()->retrieve_user_by_username($username);
    }
    
    function retrieve_group_user($group_id, $user_id)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group_id);
    	$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
    	$condition = new AndCondition($conditions);
    	
    	return GroupDataManager :: get_instance()->retrieve_group_rel_users($condition)->next_result();
    }
    
    function process_group_users($group_users)
    {
    	foreach($group_users as $group_user)
    	{
			$action = strtoupper($group_user['action']);
			switch($action)
			{
				case 'A':
					$succes = $this->create_group_user($group_user);
					break;
				case 'D':
					$succes = $group_user['group_user']->delete();
					break;
			}

			if(!$succes)
			{
				$this->failed_elements[] = Translation :: get('Failed') . ': ' . implode(";", $group_user);
			}
    	}
    }
    
    function create_group_user($group_user)
    {
    	$group_rel_user = new GroupRelUser();
    	$group_rel_user->set_group_id($group_user['group']->get_id());
    	$group_rel_user->set_user_id($group_user['username']->get_id());
    	return $group_rel_user->create();
    }
    
	function get_failed_elements()
   	{
   		return implode("<br />", $this->failed_elements);
   	}
}
?>