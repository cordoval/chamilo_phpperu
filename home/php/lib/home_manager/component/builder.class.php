<?php
/**
 * $Id: builder.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */

class HomeManagerBuilderComponent extends HomeManager implements AdministrationComponent
{
    private $build_user_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
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
                $this->display_header(null, false);
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $this->build_user_id = '0';
        }
        
        $bw = new BuildWizard($this);
        $bw->run();
    }

    function get_build_user_id()
    {
        return $this->build_user_id;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('home_builder');
    }
}
?>