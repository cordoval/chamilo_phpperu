<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/moment_table/default_moment_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../moment.class.php';
require_once dirname(__FILE__) . '/../../agreement_manager.class.php';

class InternshipPlannerMomentBrowserTableCellRenderer extends DefaultInternshipPlannerMomentTableCellRenderer
{
    
    private $browser;

    function InternshipPlannerMomentBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $moment)
    {
        if ($column === InternshipPlannerMomentBrowserTableColumnModel :: get_modification_column())
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
        
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_update_moment_url($moment), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        $toolbar_data[] = array('href' => $this->browser->get_delete_moment_url($moment), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>