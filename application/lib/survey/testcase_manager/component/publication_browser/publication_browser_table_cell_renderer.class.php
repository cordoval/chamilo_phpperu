<?php

require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_publication_table/default_survey_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../survey_publication.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager.class.php';


class TestcaseSurveyPublicationBrowserTableCellRenderer extends DefaultSurveyPublicationTableCellRenderer
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
    function TestcaseSurveyPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_publication)
    {
        if ($column === TestcaseSurveyPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_publication);
        }
        
        switch ($column->get_name())
        {
            case Translation :: get(TestcaseSurveyPublicationBrowserTableColumnModel :: COLUMN_NOT_PARTICIPANTS) :
                return $survey_publication->count_excluded_participants();
            
            case Translation :: get(TestcaseSurveyPublicationBrowserTableColumnModel :: COLUMN_PARTICIPANTS) :
                return $survey_publication->count_unique_participants();
        
        }
        
        return parent :: render_cell($column, $survey_publication);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_publication)
    {
        $survey = $survey_publication->get_publication_object();
        
        $toolbar_data = array();
        
        $user = $this->browser->get_user();
        
        $toolbar_data[] = array('href' => $this->browser->get_browse_survey_participants_url($survey_publication), 'label' => Translation :: get('Participants'), 'img' => Theme :: get_common_image_path().'action_subscribe.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_browse_survey_excluded_users_url($survey_publication), 'label' => Translation :: get('ExcludedUsers'), 'img' => Theme :: get_common_image_path().'action_unsubscribe.png');
        
        
        //$toolbar_data[] = array('href' => $this->browser->get_survey_results_viewer_url($survey_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        

        if ($user->is_platform_admin() || $user->get_id() == $survey_publication->get_publisher())
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_survey_publication_url($survey_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
            $toolbar_data[] = array('href' => $this->browser->get_update_survey_publication_url($survey_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $toolbar_data[] = array('href' => $this->browser->get_change_test_to_production_url($survey_publication), 'label' => Translation :: get('ChangeToProduction'), 'img' => Theme :: get_common_image_path() . 'export_repository.png');
            
            //TO DO implement survey exporter            
            // $toolbar_data[] = array('href' => $this->browser->get_export_survey_url($survey_publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
            //            $toolbar_data[] = array('href' => $this->browser->get_move_survey_publication_url($survey_publication), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
            //$toolbar_data[] = array('href' => $this->browser->get_publish_survey_url($survey_publication), 'label' => Translation :: get('InviteUsers'), 'img' => Theme :: get_common_image_path() . 'action_invite_users.png');
            
			$toolbar_data[] = array('href' => $this->browser->get_reporting_survey_publication_url($survey_publication), 'label' => Translation :: get('ViewReport'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
            
            $toolbar_data[] = array('href' => $this->browser->get_build_survey_url($survey_publication), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('BrowseComplex'));
        
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>