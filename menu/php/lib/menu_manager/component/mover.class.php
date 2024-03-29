<?php
namespace menu;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\AdministrationComponent;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

class MenuManagerMoverComponent extends MenuManager implements AdministrationComponent
{
	function run()
	{
		$this->check_allowed();
		$direction = Request :: get(MenuManager :: PARAM_DIRECTION);
        $category = Request :: get(MenuManager :: PARAM_ITEM);

        if (isset($direction) && isset($category))
        {
            $move_category = $this->retrieve_navigation_item($category);
            $sort = $move_category->get_sort();
            $next_category = $this->retrieve_navigation_item_at_sort($move_category->get_category(), $sort, $direction);

            if ($direction == 'up')
            {
                $move_category->set_sort($sort - 1);
                $next_category->set_sort($sort);
            }
            elseif ($direction == 'down')
            {
                $move_category->set_sort($sort + 1);
                $next_category->set_sort($sort);
            }

            if ($move_category->update() && $next_category->update())
            {
                $success = true;
            }
            else
            {
                $success = false;
            }
			
            $message = $succes ? Translation :: get('ObjectMoved', array('OBJECT' => Translation :: get('MenuManagerItem')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotMoved', array('OBJECT' => Translation :: get('MenuManagerItem')) , Utilities :: COMMON_LIBRARIES);
            $this->redirect($message , ($success ? false : true), array(
            	MenuManager :: PARAM_ACTION => MenuManager :: ACTION_BROWSE, MenuManager :: PARAM_ITEM => $move_category->get_category()));
        }
        else
        {
            $this->display_error_page(Translation :: get('NoObjectsSelected', null , Utilities :: COMMON_LIBRARIES));
        }
	}
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation :: get('MenuManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('menu_mover');
    }
    
    function get_additional_parameters()
    {
    	return array(MenuManager :: PARAM_ITEM, MenuManager :: PARAM_DIRECTION);
    }
}

?>