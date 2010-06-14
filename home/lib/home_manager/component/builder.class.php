<?php
/**
 * $Id: builder.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */

class HomeManagerBuilderComponent extends HomeManager
{
    private $build_user_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        $trail = BreadcrumbTrail :: get_instance();
        
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => HomeManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Home')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManager')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeBuilder')));
        $trail->add_help('home_build');
  
        $user = $this->get_user();
        $user_home_allowed = $this->get_platform_setting('allow_user_home');
        
        if ($user_home_allowed && Authentication :: is_valid())
        {
            $this->build_user_id = $user->get_id();
        }
        else
        {
            if (! $user->is_platform_admin())
            {
                $this->display_header($trail, false);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $this->build_user_id = '0';
        }
        
        $bw = new BuildWizard($this, $trail);
        $bw->run();
    }

    function get_build_user_id()
    {
        return $this->build_user_id;
    }
}
?>