<?php
/**
 * $Id: group_import_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class GroupImportForm extends FormValidator
{
    
    const TYPE_IMPORT = 1;
    
    private $failedcsv;
    private $current_tag;
    private $current_value;
    private $group;
    private $form_group;
    private $groups;
    private $udm;
    private $doc;

    /**
     * Creates a new GroupImportForm 
     * Used to import groups from a file
     */
    function GroupImportForm($form_type, $action, $form_group)
    {
        parent :: __construct('group_import', 'post', $action);
        
        $this->form_group = $form_group;
        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_IMPORT)
        {
            $this->build_importing_form();
        }
    }

    function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        $allowed_upload_types = array('xml');
        $this->addRule('file', Translation :: get('OnlyXMLAllowed'), 'filetype', $allowed_upload_types);
        
        //$this->addElement('submit', 'group_import', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function import_groups()
    {
        $values = $this->exportValues();
        $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
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
        
        //print_r($groups); echo "<br/><br/>";
        $this->create_groups($groups, 0);
    }

    function parse_group($group)
    {
        $group_array = array();
        
        if ($group->hasChildNodes())
        {
            $group_array['name'] = $group->getElementsByTagName('name')->item(0)->nodeValue;
            $group_array['description'] = $group->getElementsByTagName('description')->item(0)->nodeValue;
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

    function create_groups($groups, $parent_group)
    {
        foreach ($groups as $gr)
        {
            $group = new Group();
            $group->set_name($gr['name']);
            $group->set_description($gr['description']);
            $group->set_parent($parent_group);
            $group->create();
            
            $this->create_groups($gr['children'], $group->get_id());
        }
    }

}
?>