<?php
/**
 * $Id: group_import_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class GroupImportForm extends FormValidator
{
    private $form_group;
    private $doc;
    private $failed_elements;

    /**
     * Creates a new GroupImportForm 
     * Used to import groups from a file
     */
    function GroupImportForm($action, $form_group)
    {
        parent :: __construct('group_import', 'post', $action);
        
        $this->form_group = $form_group;
        $this->failed_elements = array();
        $this->build_importing_form();
    }

    function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        $allowed_upload_types = array('xml');
        $this->addRule('file', Translation :: get('OnlyXMLAllowed'), 'filetype', $allowed_upload_types);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function import_groups()
    {
        $values = $this->exportValues();
        $groups = $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
        
        foreach($groups as $group)
        {
        	$this->validate_group($group);
        }

        if(count($this->failed_elements) > 0)
        	return false;
        
        $this->process_groups($groups);
        
        if(count($this->failed_elements) > 0)
        	return false;
        
        return true;
    }
    
    function parse_file($file)
    {
        $this->doc = new DOMDocument();
        $this->doc->load($file);
        $group_root = $this->doc->getElementsByTagname('groups')->item(0);
        
        $group_nodes = $group_root->childNodes;
        foreach ($group_nodes as $node)
        {
            if ($node->nodeName == "#text")
                continue;
            
            $groups[] = $this->parse_group($node);
        }
        
        return $groups;
    }

    function parse_group($group)
    {
        $group_array = array();
        
        if ($group->hasChildNodes())
        {
            $group_array['action'] = $group->getElementsByTagName('action')->item(0)->nodeValue;
        	$group_array['name'] = $group->getElementsByTagName('name')->item(0)->nodeValue;
            $group_array['description'] = $group->getElementsByTagName('description')->item(0)->nodeValue;
            $group_array['code'] = $group->getElementsByTagName('code')->item(0)->nodeValue;
            $children = $group->getElementsByTagName('children')->item(0);
            
            $group_nodes = $children->childNodes;
            foreach ($group_nodes as $node)
            {
                if ($node->nodeName == "#text")
                    continue;
                
                $group_array['children'][] = $this->parse_group($node);
            }
        
        }
        
        return $group_array;
    }

    function validate_group($group)
    {
    	//1. Check if action is valid
    	$action = strtoupper($group['action']);
    	if($action != 'A' && $action != 'U' && $action != 'D')
    	{
    		$this->failed_elements[] = Translation :: get('Invalid') . ': ' . $this->display_group($group);
    		return $this->validate_children($group['children']);
    	}
    	
    	//2. Check if name & code is filled in
    	if(!$group['name'] || $group['name'] == '' || !$group['code'] || $group['code'] == '')
    	{ 
    		$this->failed_elements[] = Translation :: get('Invalid') . ': ' . $this->display_group($group);
    		return $this->validate_children($group['children']);
    	}
    	
    	//3. Check if action is valid
    	if( ($action == 'A' && $this->group_code_exists($group['code'])) || 
    		($action != 'A' && !$this->group_code_exists($group['code']) ))
    	{
    		$this->failed_elements[] = Translation :: get('Invalid') . ': ' . $this->display_group($group);
    		return $this->validate_children($group['children']);
    	}
    	
    	return $this->validate_children($group['children']);
    }
    
    function validate_children($children)
    {
    	foreach($children as $child)
    	{
    		$this->validate_group($child);
    	}
    }
    
    function process_groups($groups, $parent_group = 1)
    {
        foreach ($groups as $gr)
        {
        	$action = strtoupper($gr['action']);
            
            switch($action)
            {
            	case 'A':
            		$group = $this->create_group($gr, $parent_group);
            		break;
            	case 'U':
            		$group = $this->update_group($gr, $parent_group);
            		break;
            	case 'D':
            		$group = $this->delete_group($gr);
            		break;	
            }
            
            if(!$group)
            {
            	$this->failed_elements[] = Translation :: get('Failed') . ': ' . $this->display_group($group);
            	return;
            }
            
            $this->process_groups($gr['children'], $group->get_id());
        }
    }
    
    function display_group($group)
    {
    	return $group['code'] . ' - ' . $group['name'];
    }
    
    function create_group($data, $parent_group)
    {
    	$group = new Group();
    	$group->set_name($data['name']);
        $group->set_description($data['description']);
        $group->set_code($data['code']);
        $group->set_parent($parent_group);
        
        if($group->create())
        	return $group;
    }
    
    function update_group($data, $parent_group)
    {
    	$group = $this->get_group($data['code']);
    	$group->set_name($data['name']);
        $group->set_description($data['description']);
        $succes = $group->update();
        
        if($group->get_parent() != $parent_group)
        {
        	$succes &= $group->move($parent_group);
        }
        
        if($succes)
        	return $group;
    }
    
    function delete_group($data)
    {
    	$group = $this->get_group($data['code']);
    	
    	//Group is already deleted by parent deletion
    	if(!$group)
    		return false;
    	
    	if($group->delete())
        	return $group;
    }

   	function get_group($code)
   	{
   		$condition = new EqualityCondition(Group :: PROPERTY_CODE, $code);
   		$groups = GroupDataManager :: get_instance()->retrieve_groups($condition);
   		return $groups->next_result();
   	}
    
   	function group_code_exists($code)
   	{
   		return !is_null($this->get_group($code));
   	}
   	
   	function get_failed_elements()
   	{
   		return implode("<br />", $this->failed_elements);
   	}
}
?>