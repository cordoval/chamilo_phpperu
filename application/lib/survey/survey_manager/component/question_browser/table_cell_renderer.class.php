<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_question_table/default_survey_question_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyQuestionBrowserTableCellRenderer extends DefaultSurveyQuestionTableCellRenderer
{
    /**
     * The browser component
     * @var SurveyManagerSurveyQuestionsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function SurveyQuestionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_question)
    {
        if ($column === SurveyQuestionBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_question);
        }
        
        return parent :: render_cell($column, $survey_question);
    }

    /**
     * Gets the action links to display
     * @param SurveyQuestion $survey_question The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_question)
    {
//        $survey = $survey_question->get_publication_object();
//        
//        $toolbar_data = array();
//        
//        $user = $this->browser->get_user();
//        
//        if ($survey_question->is_visible_for_target_user($user, true))
//        {
//            $toolbar_data[] = array('href' => $this->browser->get_survey_question_viewer_url($survey_question), 'label' => Translation :: get('TakeSurvey'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
//        }
//        else
//        {
//            $toolbar_data[] = array('label' => Translation :: get('SurveyPublished'), 'img' => Theme :: get_common_image_path() . 'action_next_na.png');
//        
//        }
        
//        $toolbar_data[] = array('href' => $this->browser->get_survey_results_viewer_url($survey_question), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
//        if ($user->is_platform_admin() || $user->get_id() == $survey_question->get_publisher())
//        {
//            $toolbar_data[] = array('href' => $this->browser->get_delete_survey_question_url($survey_question), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
//            $toolbar_data[] = array('href' => $this->browser->get_update_survey_question_url($survey_question), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
//            
//            if ($survey_question->get_hidden())
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_change_survey_question_visibility_url($survey_question), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_visible_na.png');
//            }
//            else
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_change_survey_question_visibility_url($survey_question), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
//            }
//            
//            $toolbar_data[] = array('href' => $this->browser->get_reporting_survey_question_url($survey_question), 'label' => Translation :: get('ViewReport'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
//            
//            //TO DO implement survey exporter !!
//            //$toolbar_data[] = array('href' => $this->browser->get_export_survey_url($survey_question), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
//            $toolbar_data[] = array('href' => $this->browser->get_move_survey_question_url($survey_question), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
//            $toolbar_data[] = array('href' => $this->browser->get_mail_survey_participant_url($survey_question), 'label' => Translation :: get('InviteParticipants'), 'img' => Theme :: get_common_image_path() . 'action_invite_users.png');
//            
//            if ($survey->is_complex_content_object())
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_build_survey_url($survey_question), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('BrowseSurvey'));
//            }
//        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>