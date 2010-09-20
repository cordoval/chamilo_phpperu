<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/mentor_rel_location_table/default_mentor_rel_location_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../mentor_rel_location.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';
require_once dirname(__FILE__) . '/../../../region_manager/region_manager.class.php';

class InternshipOrganizerMentorRelLocationBrowserTableCellRenderer extends DefaultInternshipOrganizerMentorRelLocationTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMentorRelLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $mentor_rel_location)
    {
        if ($column === InternshipOrganizerMentorRelLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($mentor_rel_location);
        }
        
        switch ($column->get_name())
        {
//            case InternshipOrganizerMentorRelLocation :: PROPERTY_NAME :
//                $title = parent :: render_cell($column, $mentor_rel_location);
//                $title_short = $title;
//                
//                if (strlen($title_short) > 53)
//                {
//                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
//                }
//                return '<a href="' . htmlentities($this->browser->get_view_mentor_rel_location_url($mentor_rel_location)) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $mentor_rel_location);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($mentor_rel_location)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_unsubscribe_location_url($mentor_rel_location), ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }

}
?>