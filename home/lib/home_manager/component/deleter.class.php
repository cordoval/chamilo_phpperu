<?php
/**
 * $Id: deleter.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerDeleterComponent extends HomeManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $id = Request :: get(HomeManager :: PARAM_HOME_ID);
        $type = Request :: get(HomeManager :: PARAM_HOME_TYPE);
        $trail = BreadcrumbTrail :: get_instance();;
        
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeDeleter')));
        $trail->add_help('home general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if ($id && $type)
        {
            switch ($type)
            {
                case HomeManager :: TYPE_BLOCK :
                    $object = $this->retrieve_home_block($id);
                    break;
                case HomeManager :: TYPE_COLUMN :
                    $object = $this->retrieve_home_column($id);
                    break;
                case HomeManager :: TYPE_ROW :
                    $object = $this->retrieve_home_row($id);
                    break;
                case HomeManager :: TYPE_TAB :
                    $object = $this->retrieve_home_tab($id);
                    break;
            }
            
            if (! $object->delete())
            {
                $success = false;
            }
            else
            {
                $success = true;
            }
            
            $this->redirect(Translation :: get($success ? 'HomeDeleted' : 'HomeNotDeleted'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>