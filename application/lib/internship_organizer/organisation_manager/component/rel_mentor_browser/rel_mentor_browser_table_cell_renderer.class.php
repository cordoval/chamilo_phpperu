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
        
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case InternshipOrganizerMentor :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $rel_location);
                $title_short = $title;
                if (strlen($title_short) > 75)
                {
                    $title_short = mb_substr($title_short, 0, 75) . '&hellip;';
                }
                $mentor = InternshipOrganizerDataManager :: get_instance()->retrieve_mentor($rel_location->get_mentor_id());
                return '<a href="' . htmlentities($this->browser->get_view_mentor_url($mentor)) . '" title="' . $title . '">' . $title_short . '</a>';
        
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