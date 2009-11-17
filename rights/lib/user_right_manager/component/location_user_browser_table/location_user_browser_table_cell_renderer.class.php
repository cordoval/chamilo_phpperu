<?php
/**
 * $Id: location_user_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.user_right_manager.component.location_user_bowser_table
 */
require_once dirname(__FILE__) . '/location_user_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell renderer for the user object browser table
 */
class LocationUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === LocationUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        if (LocationUserBrowserTableColumnModel :: is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar_data = array();
        return Utilities :: build_toolbar($toolbar_data);
    }

    private function get_rights_column_value($column, $user)
    {
        $browser = $this->browser;
        $location = $browser->get_location();
        $locked_parent = $location->get_locked_parent();
        $rights = RightsUtilities :: get_available_rights($this->browser->get_source());
        $user_id = $user->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $location->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_SET_USER_RIGHTS, 'user_id' => $user_id, 'right_id' => $right_id, UserRightManager :: PARAM_LOCATION => $location->get_id()));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $user, $location);
            }
        }
        return '&nbsp;';
    }
}
?>