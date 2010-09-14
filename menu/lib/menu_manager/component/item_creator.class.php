<?php

require_once dirname(__FILE__) . '/creator.class.php';

class MenuManagerItemCreatorComponent extends MenuManagerCreatorComponent implements AdministrationComponent
{	
	function get_creation_form($item)
	{
		return new NavigationItemForm(NavigationItemForm :: TYPE_CREATE, $item, $this->get_url());
	}
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation :: get('MenuManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('menu_item_creator');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM);
    }
}

?>