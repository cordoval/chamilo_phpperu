<?php
/**
 * $Id: admin_event_browser.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component for viewing tracker events
 */
class TrackingManagerAdminEventBrowserComponent extends TrackingManager implements AdministrationComponent
{

	private $action_bar;
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!TrackingRights :: is_allowed_in_tracking_subtree(TrackingRights :: VIEW_RIGHT, 0, 0))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $this->display_header();
        
        echo $this->action_bar->as_html();
        
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
        $table = new EventBrowserTable($this, null, array(Application :: PARAM_APPLICATION => TrackingManager :: APPLICATION_NAME, Application :: PARAM_ACTION => TrackingManager :: ACTION_BROWSE_EVENTS), $this->get_condition());
        
        $html = array();
        $html[] = '<div>';
        $html[] = $table->as_html();
        $html[] = '</div>';
        
        return implode($html, "\n");
    }
    
    function get_condition()
    {
    	return $this->action_bar->get_conditions(array(new ConditionProperty(Event :: PROPERTY_NAME)));
    }
    
	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if(TrackingRights :: is_allowed_in_tracking_subtree(TrackingRights :: EDIT_RIGHT, 0))
        {
        	$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_manage_rights_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));	
        }
        
        return $action_bar;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('tracking_event_browser');
    }

}
?>