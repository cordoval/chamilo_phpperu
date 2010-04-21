<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/mentor_table/default_mentor_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../mentor.class.php';
require_once dirname(__FILE__) . '/../../mentor_manager.class.php';

class InternshipOrganizerMentorBrowserTableCellRenderer extends DefaultInternshipOrganizerMentorTableCellRenderer
{
    
    private $browser;

    function InternshipOrganizerMentorBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $mentor)
    {
        if ($column === InternshipOrganizerMentorBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($mentor);
        }
        
        return parent :: render_cell($column, $mentor);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($mentor)
    {
        
        $toolbar_data = array();
        
        $user = $this->browser->get_user();
        
      
            $toolbar_data[] = array('href' => $this->browser->get_update_mentor_url($mentor), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
            $toolbar_data[] = array('href' => $this->browser->get_delete_mentor_url($mentor), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
            $toolbar_data[] = array('href' => $this->browser->get_view_mentor_url($mentor), 'label' => Translation :: get('View'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
            
      
        
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>