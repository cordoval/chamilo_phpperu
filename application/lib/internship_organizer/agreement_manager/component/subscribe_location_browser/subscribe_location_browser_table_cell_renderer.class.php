<?php

require_once dirname(__FILE__) . '/subscribe_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_cell_renderer.class.php';

class InternshipOrganizerSubscribeLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerSubscribeLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === InternshipOrganizerSubscribeLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }
        
        return parent :: render_cell($column, $location);
    }

    function render_id_cell($location)
    {
        $agreement = $this->browser->get_agreement();
        return $agreement->get_id() . '|' . $location->get_id();
    }

    /**
     * Gets the action links to display
     * @param Location $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {
        $agreement = $this->browser->get_agreement();
        $toolbar = new Toolbar();
        
        $subscribe_url = $this->browser->get_agreement_rel_location_subscribing_url($agreement, $location);
        $toolbar->add_item(new ToolbarItem(Translation :: get('Subscribe'), Theme :: get_common_image_path() . 'action_subscribe.png', $subscribe_url, ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>