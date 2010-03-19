<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../location.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipLocationBrowserTableCellRenderer extends DefaultInternshipLocationTableCellRenderer
{
   
    private $browser;

    
    function InternshipLocationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $location)
    {
        if ($column === InternshipLocationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($location);
        }
        
        return parent :: render_cell($column, $location);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($location)
    {
               
        $toolbar_data = array();
        
        $user = $this->browser->get_user();
        
//        if ($survey_publication->is_visible_for_target_user($user, true))
//        {
//            $toolbar_data[] = array('href' => $this->browser->get_survey_publication_viewer_url($survey_publication), 'label' => Translation :: get('TakeSurvey'), 'img' => Theme :: get_common_image_path() . 'action_next.png');
//        }
//        else
//        {
//            $toolbar_data[] = array('label' => Translation :: get('SurveyPublished'), 'img' => Theme :: get_common_image_path() . 'action_next_na.png');
//        
//        }
//        
//        $toolbar_data[] = array('href' => $this->browser->get_survey_results_viewer_url($survey_publication), 'label' => Translation :: get('ViewResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
//        
//        if ($user->is_platform_admin() || $user->get_id() == $survey_publication->get_publisher())
//        {
//            $toolbar_data[] = array('href' => $this->browser->get_delete_survey_publication_url($survey_publication), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png');
//            $toolbar_data[] = array('href' => $this->browser->get_update_survey_publication_url($survey_publication), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
//            
//            if ($survey_publication->get_hidden())
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_change_survey_publication_visibility_url($survey_publication), 'label' => Translation :: get('Show'), 'img' => Theme :: get_common_image_path() . 'action_visible_na.png');
//            }
//            else
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_change_survey_publication_visibility_url($survey_publication), 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
//            }
//            
//            $toolbar_data[] = array('href' => $this->browser->get_export_survey_url($survey_publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
//            $toolbar_data[] = array('href' => $this->browser->get_move_survey_publication_url($survey_publication), 'label' => Translation :: get('Move'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
//            $toolbar_data[] = array('href' => $this->browser->get_publish_survey_url($survey_publication), 'label' => Translation :: get('InviteUsers'), 'img' => Theme :: get_common_image_path() . 'action_invite_users.png');
//            
//            if ($survey->is_complex_content_object())
//            {
//                $toolbar_data[] = array('href' => $this->browser->get_build_survey_url($survey_publication), 'img' => Theme :: get_common_image_path() . 'action_browser.png', 'label' => Translation :: get('BrowseComplex'));
//            }
//        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>