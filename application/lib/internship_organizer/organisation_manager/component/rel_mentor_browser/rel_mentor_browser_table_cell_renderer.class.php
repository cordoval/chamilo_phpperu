<?php

require_once dirname(__FILE__) . '/rel_mentor_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/mentor_rel_location_table/default_mentor_rel_location_table_cell_renderer.class.php';

class InternshipOrganizerMentorRelLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerMentorRelLocationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMentorRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_location)
    {
        if ($column === InternshipOrganizerMentorRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            //return $this->get_modification_links( $rel_location);
        }
        
        return parent :: render_cell($column, $rel_location);
    }

    private function get_modification_links($rel_location)
    {
        $toolbar_data = array();
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>