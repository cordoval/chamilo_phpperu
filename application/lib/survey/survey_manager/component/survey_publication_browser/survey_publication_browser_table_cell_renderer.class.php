<?php
/**
 * $Id: survey_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_publication_browser
 */
require_once dirname(__FILE__) . '/survey_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_publication_table/default_survey_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../survey_publication.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author 
 */

class SurveyPublicationBrowserTableCellRenderer extends DefaultSurveyPublicationTableCellRenderer
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
    function SurveyPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_publication)
    {
        if ($column === SurveyPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_publication);
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
        
        if ($survey_publication->is_visible_for_target_user($user, true))
        {
            $toolbar_data[] = array('href' => $this->browser->get_survey_publication_viewer_url($survey_publication), 'label' => Translation :: get('TakeSurvey'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('SurveyPublished'), 'img' => Theme :: get_common_image_path() . 'action_next_na.png');
        
        }
        
        $toolbar_data[] = array('href' => $this->browser->get_survey_results_viewer_url($survey_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
        if ($user->is_platform_admin() || $user->get_id() == $survey_publication->get_publisher())
        {
            $toolbar_data[] = array('href' => $this->browser->get_delete_survey_publication_url($survey_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
            $toolbar_data[] = array('href' => $this->browser->get_update_survey_publication_url($survey_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            if ($survey_publication->get_hidden())
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_survey_publication_visibility_url($survey_publication), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_visible_na.png');
            }
            else
            {
                $toolbar_data[] = array('href' => $this->browser->get_change_survey_publication_visibility_url($survey_publication), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
            }
            
            //TO DO implement survey exporter !!
            //$toolbar_data[] = array('href' => $this->browser->get_export_survey_url($survey_publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
            $toolbar_data[] = array('href' => $this->browser->get_move_survey_publication_url($survey_publication), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
            $toolbar_data[] = array('href' => $this->browser->get_mail_survey_participant_url($survey_publication), 'label' => Translation :: get('InviteUsers'), 'img' => Theme :: get_common_image_path() . 'action_invite_users.png');
            
            if ($survey->is_complex_content_object())
            {
                $toolbar_data[] = array('href' => $this->browser->get_build_survey_url($survey_publication), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('BrowseComplex'));
            }
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>