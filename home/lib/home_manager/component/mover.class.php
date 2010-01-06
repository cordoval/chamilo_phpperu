<?php
/**
 * $Id: mover.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerMoverComponent extends HomeManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $id = Request :: get(HomeManager :: PARAM_HOME_ID);
        $type = Request :: get(HomeManager :: PARAM_HOME_TYPE);
        $direction = Request :: get(HomeManager :: PARAM_DIRECTION);
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => HomeManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Home')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManager')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeMover')));
        $trail->add_help('home general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if ($id && $type)
        {
            $url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => $type, HomeManager :: PARAM_HOME_ID => $id));
            switch ($type)
            {
                case HomeManager :: TYPE_BLOCK :
                    $move_home = $this->retrieve_home_block($id);
                    $sort = $move_home->get_sort();
                    $next_home = $this->retrieve_home_block_at_sort($move_home->get_column(), $sort, $direction);
                    break;
                case HomeManager :: TYPE_COLUMN :
                    $move_home = $this->retrieve_home_column($id);
                    $sort = $move_home->get_sort();
                    $next_home = $this->retrieve_home_column_at_sort($move_home->get_row(), $sort, $direction);
                    break;
                case HomeManager :: TYPE_ROW :
                    $move_home = $this->retrieve_home_row($id);
                    $sort = $move_home->get_sort();
                    $next_home = $this->retrieve_home_row_at_sort($move_home->get_tab(), $sort, $direction);
                    break;
                case HomeManager :: TYPE_TAB :
                    $move_home = $this->retrieve_home_tab($id);
                    $sort = $move_home->get_sort();
                    $next_home = $this->retrieve_home_tab_at_sort($move_home->get_user(), $sort, $direction);
                    break;
            }
            
            if ($direction == 'up')
            {
                $move_home->set_sort($sort - 1);
                $next_home->set_sort($sort);
            }
            elseif ($direction == 'down')
            {
                $move_home->set_sort($sort + 1);
                $next_home->set_sort($sort);
            }
            
            if ($move_home->update() && $next_home->update())
            {
                $success = true;
            }
            else
            {
                $success = false;
            }
            
            $this->redirect(Translation :: get($success ? 'HomeMoved' : 'HomeNotMoved'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>