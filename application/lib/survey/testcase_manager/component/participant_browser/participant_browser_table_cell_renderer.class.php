<?php

require_once dirname(__FILE__) . '/participant_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/participant_table/default_participant_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../trackers/survey_participant_tracker.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class TestcaseSurveyParticipantBrowserTableCellRenderer extends DefaultParticipantTableCellRenderer
{
    /**
     * The browser component
     * @var SurveyManagerSurveyPublicationsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function TestcaseSurveyParticipantBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_participant_tracker)
    {
        if ($column === TestcaseSurveyParticipantBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_participant_tracker);
        }
        
        return parent :: render_cell($column, $survey_participant_tracker);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_participant_tracker)
    {
        
        $survey_publication = SurveyDataManager::get_instance()->retrieve_survey_publication($survey_participant_tracker->get_survey_publication_id());
    	
        $toolbar_data = array();
           
      
        $toolbar_data[] = array('href' => $this->browser->get_test_survey_publication_viewer_url($survey_participant_tracker), 'label' => Translation :: get('TakeSurvey'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
         
        $toolbar_data[] = array('href' => $this->browser->get_survey_results_viewer_url($survey_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>