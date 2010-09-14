<?php
class MenuManagerDeleterComponent extends MenuManager implements AdministrationComponent
{
	function run()
	{
		$this->check_allowed();
		$navigation_item_id = Request :: get(MenuManager :: PARAM_ITEM);
        $parent = 0;
        $failures = 0;

        if (! empty($navigation_item_id))
        {
            if (! is_array($navigation_item_id))
            {
                $navigation_item_id = array($navigation_item_id);
            }

            foreach ($navigation_item_id as $id)
            {
                $category = $this->retrieve_navigation_item($id);
                $parent = $category->get_category();

                if (! $category->delete())
                {
                    $failures ++;
                }
            }
            
            $message = $this->get_result($failures, count($navigation_item_id), 'SelectedItemNotDeleted', 'SelectedItemsNotDeleted', 'SelectedItemDeleted', 'SelectedItemsDeleted');
            
            $this->redirect($message, ($failures ? true : false), array(MenuManager :: PARAM_ACTION => MenuManager :: ACTION_BROWSE, MenuManager :: PARAM_ITEM => $parent));

        }
        else
        {
            $this->display_error_page(Translation :: get('NoObjectsSelected'));
        }
	}
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation :: get('MenuManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('menu_deleter');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM, MenuManager :: PARAM_DIRECTION);
    }
}

?>