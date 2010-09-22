<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../location.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';
require_once dirname(__FILE__) . '/../../../region_manager/region_manager.class.php';

class InternshipOrganizerLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerLocationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === InternshipOrganizerLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }
        
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $location);
                $title_short = $title;
                
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_view_location_url($location)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $location);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_location_url($location), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_location_url($location), ToolbarItem :: DISPLAY_ICON, true));
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_location_url($location), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }

}
?>