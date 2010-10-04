<?php

require_once dirname(__FILE__) . '/creator.class.php';

class MenuManagerCategoryCreatorComponent extends MenuManagerCreatorComponent implements AdministrationComponent
{
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation :: get('MenuManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('menu_category_creator');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM);
    }
    
	function get_creation_form($item)
	{
		return new NavigationItemCategoryForm(NavigationItemCategoryForm :: TYPE_CREATE, $item, $this->get_url());
	}
}

?>