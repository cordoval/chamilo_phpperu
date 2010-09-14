<?php

require_once dirname(__FILE__) . '/editor.class.php';

class MenuManagerItemEditorComponent extends MenuManagerEditorComponent implements AdministrationComponent
{
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation :: get('MenuManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('menu_item_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM);
    }
    
    function get_edit_form($item)
    {
    	return new NavigationItemForm(NavigationItemForm :: TYPE_EDIT, $item, $this->get_url());
    }
}

?>