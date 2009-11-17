<?php
/**
 * $Id: location_rights_template_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component.location_rights_template_browser_table
 */
require_once dirname(__FILE__) . '/location_rights_template_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/rights_template_table/default_rights_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationRightsTemplateBrowserTableCellRenderer extends DefaultRightsTemplateTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationRightsTemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rights_template)
    {
        if ($column === LocationRightsTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rights_template);
        }
        
        if (LocationRightsTemplateBrowserTableColumnModel :: is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $rights_template);
        }
        
        return parent :: render_cell($column, $rights_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($rights_template)
    {
        $toolbar_data = array();
        return Utilities :: build_toolbar($toolbar_data);
    }

    private function get_rights_column_value($column, $rights_template)
    {
        $browser = $this->browser;
        $location = $browser->get_location();
        $locked_parent = $location->get_locked_parent();
        $rights = RightsUtilities :: get_available_rights($this->browser->get_source());
        $rights_template_id = $rights_template->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $location->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_SET_USER_RIGHTS, 'rights_template_id' => $rights_template_id, 'right_id' => $right_id, RightsTemplateManager :: PARAM_LOCATION => $location->get_id()));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $rights_template, $location);
            }
        }
        return '&nbsp;';
    }
}
?>