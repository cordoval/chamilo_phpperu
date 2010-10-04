<?php

require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/publication_table/default_survey_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../survey_publication.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

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
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $survey_publication);
                if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION))
                {
                    $url = '<a href="' . htmlentities($this->browser->get_survey_publication_viewer_url($survey_publication)) . '" title="' . $title . '">' . $title . '</a>';
                }
                else
                {
                    $url = $title;
                }
                return $url;
        }
        
        return parent :: render_cell($column, $survey_publication);
    
    }
    
    private function get_modification_links($survey_publication)
    {
        $survey = $survey_publication->get_publication_object();
        $user = $this->browser->get_user();
        $user_id = $user->get_id();
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_PARTICIPATE, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_next.png', $this->browser->get_survey_publication_viewer_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('SurveyPublished'), Theme :: get_common_image_path() . 'action_next_na.png', null, ToolbarItem :: DISPLAY_ICON));
        
        }
        
        if ($user->is_platform_admin() || $user->get_id() == $survey_publication->get_publisher())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_rights_editor_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
            
        //            if ($survey instanceof ComplexContentObjectSupport)
        //            {
        //                $toolbar->add_item(new ToolbarItem(Translation :: get('BrowseSurvey'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_build_survey_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        //            }
        }
        
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_EDIT, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_survey_publication_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_DELETE, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_survey_publication_url($survey_publication), ToolbarItem :: DISPLAY_ICON, true));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_VIEW, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Participants'), Theme :: get_common_image_path() . 'action_subscribe.png', $this->browser->get_browse_survey_participants_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_REPORTING, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ViewReport'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_reporting_survey_publication_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('InviteParticipants'), Theme :: get_common_image_path() . 'action_invite_users.png', $this->browser->get_mail_survey_participant_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_REPORTING, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            //$toolbar->add_item(new ToolbarItem(Translation :: get('BrowseSurveyPages'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_browse_survey_pages_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        	$toolbar->add_item(new ToolbarItem(Translation :: get('ReportingFilter'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_reporting_filter_survey_publication_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_EXPORT_RESULT, $survey_publication->get_id(), SurveyRights :: TYPE_PUBLICATION, $user_id))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ExportToExcel'), Theme :: get_common_image_path() . 'export_excel.png', $this->browser->get_survey_publication_export_excel_url($survey_publication), ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}

?>