<?php
/**
 * $Id: admin_event_browser.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component for viewing tracker events
 */
class TrackingManagerAdminEventBrowserComponent extends TrackingManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => TrackingManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Tracking')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('EventsList')));
        $trail->add_help('tracking general');
        
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail);
        
        $isactive = (PlatformSetting :: get('enable_tracking', 'tracking') == 1);
        
        if ($isactive)
        {
            $output = $this->get_user_html();
            echo ($output);
        }
        else
        {
            $this->display_error_message('<a href="' . $this->get_platform_administration_link() . '">' . Translation :: get('Tracking_is_disabled') . '</a>');
        }
        
        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new EventBrowserTable($this, null, array(Application :: PARAM_APPLICATION => TrackingManager :: APPLICATION_NAME, Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS), null);
        
        $html = array();
        $html[] = '<div>';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }

}
?>