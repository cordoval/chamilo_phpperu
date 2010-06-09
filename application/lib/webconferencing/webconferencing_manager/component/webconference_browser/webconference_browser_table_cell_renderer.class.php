<?php
/**
 * $Id: webconference_browser_table_cell_renderer.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager.component.webconference_browser
 */
require_once dirname(__FILE__) . '/webconference_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/webconference_table/default_webconference_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../webconference.class.php';
require_once dirname(__FILE__) . '/../../webconferencing_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Stefaan Vanbillemont
 */

class WebconferenceBrowserTableCellRenderer extends DefaultWebconferenceTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function WebconferenceBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $webconference)
    {
        if ($column === WebconferenceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($webconference);
        }
        
        //Add special features here
        switch ($column->get_name())
        {
            case Webconference :: PROPERTY_CONFNAME :
                $confname = parent :: render_cell($column, $webconference);
                $confkey = $webconference->get_confkey();
                $confname = $webconference->get_confname();
                
                //retrieve dimdim server settins
                $server = PlatformSetting :: get('dimdim_server_url', WebconferencingManager :: APPLICATION_NAME);
                $port = PlatformSetting :: get('dimdim_server_port', WebconferencingManager :: APPLICATION_NAME);
                
                //retrieve user information
                $user_email = $this->browser->get_user()->get_email();
                $user_displayname = $this->browser->get_user()->get_firstname() . $this->browser->get_user()->get_lastname();
                
                //start building url
                $view_url = 'http://' . $server . ':' . $port;
                if ($this->browser->get_user()->is_platform_admin() || $webconference->get_user_id() == $this->browser->get_user()->get_id())
                {
                    
                    $view_url = $view_url . '/portal/start.action?';
                    $view_url = $view_url . 'email=' . $user_email;
                    $view_url = $view_url . '&displayName=' . $user_displayname;
                    $view_url = $view_url . '&confKey=' . $confkey;
                    $view_url = $view_url . '&confName=' . $confname;
                    //loop all webconference_options and add them to the view_url
                    $condition = new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id());
                    $options = WebconferencingDataManager :: get_instance()->retrieve_webconference_options($condition);
                    while ($option = $options->next_result())
                    {
                        $view_url = $view_url . '&' . $option->get_name() . '=' . $option->get_value();
                    }
                }
                else
                {
                    $view_url = $view_url . '/portal/join.action?';
                    $view_url = $view_url . 'email=' . $user_email;
                    $view_url = $view_url . '&displayName=' . $user_displayname;
                    $view_url = $view_url . '&confKey=' . $confkey;
                    
                    //if value set for attendeePassCode, moderatorPassCode, attendeePwd, presenterPwd
                    //add parameter to the url
                    $conf_condition = new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id());
                    $option_condition = new EqualityCondition(WebconferenceOption :: PROPERTY_NAME, 'attendeePasscode');
                    $option_values = WebconferencingDataManager :: get_instance()->retrieve_webconference_options(new AndCondition($conf_condition, $option_condition));
                    if ($option_values->size() == 1)
                    {
                        $option = $option_values->next_result();
                        $view_url = $view_url . '&attendeePassCode=' . $option->get_value();
                    }
                    
                    $option_condition = new EqualityCondition(WebconferenceOption :: PROPERTY_NAME, 'moderatorPassCode');
                    $option_values = WebconferencingDataManager :: get_instance()->retrieve_webconference_options(new AndCondition($conf_condition, $option_condition));
                    if ($option_values->size() == 1)
                    {
                        $option = $option_values->next_result();
                        $view_url = $view_url . '&moderatorPassCode=' . $option->get_value();
                    }
                    
                    $option_condition = new EqualityCondition(WebconferenceOption :: PROPERTY_NAME, 'attendeePwd');
                    $option_values = WebconferencingDataManager :: get_instance()->retrieve_webconference_options(new AndCondition($conf_condition, $option_condition));
                    if ($option_values->size() == 1)
                    {
                        $option = $option_values->next_result();
                        $view_url = $view_url . '&attendeePwd=' . $option->get_value();
                    }
                    
                    $option_condition = new EqualityCondition(WebconferenceOption :: PROPERTY_NAME, 'presenterPwd');
                    $option_values = WebconferencingDataManager :: get_instance()->retrieve_webconference_options(new AndCondition($conf_condition, $option_condition));
                    if ($option_values->size() == 1)
                    {
                        $option = $option_values->next_result();
                        $view_url = $view_url . '&presenterPwd=' . $option->get_value();
                    }
                }
                //add returnUrl
                $view_url = $view_url . '&returnUrl=' . Path :: get('WEB_PATH');
                
                return '<a href="' . htmlentities($view_url) . '" target="_blank" title="' . $confname . '">' . $confname . '</a>';
                break;
        }
        
        return parent :: render_cell($column, $webconference);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($webconference)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($this->browser->get_user()->is_platform_admin() || $webconference->get_user_id() == $this->browser->get_user()->get_id())
        {
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Edit'),
	        		Theme :: get_common_image_path() . 'action_edit.png',
	        		$this->browser->get_update_webconference_url($webconference),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
	        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Delete'),
	        		Theme :: get_common_image_path() . 'action_delete.png',
	        		$this->browser->get_delete_webconference_url($webconference),
	        		ToolbarItem :: DISPLAY_ICON,
	        		true
	        ));
        }
        
        return $toolbar->as_html();
    }
}
?>