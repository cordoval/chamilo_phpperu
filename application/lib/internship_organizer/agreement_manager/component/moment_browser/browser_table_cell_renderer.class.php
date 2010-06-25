<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/moment_table/default_moment_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../moment.class.php';
require_once dirname(__FILE__) . '/../../agreement_manager.class.php';

class InternshipOrganizerMomentBrowserTableCellRenderer extends DefaultInternshipOrganizerMomentTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMomentBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $moment)
    {
        if ($column === InternshipOrganizerMomentBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($moment);
        }
        
        return parent :: render_cell($column, $moment);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($moment)
    {
        
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_moment_url($moment), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_moment_url($moment), ToolbarItem :: DISPLAY_ICON, true));
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_moment_url($moment), ToolbarItem :: DISPLAY_ICON ));  
        
        return $toolbar->as_html();
    }
}
?>