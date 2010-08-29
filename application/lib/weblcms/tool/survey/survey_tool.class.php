<?php
/**
 * $Id: survey_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
require_once Path :: get_application_path() . 'lib/weblcms/tool/tool.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class SurveyTool extends Tool
{
    const ACTION_DELETE_PUBLICATION = 'delete_pub';
    const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
    const ACTION_VIEW_RESULTS = 'result';
    const ACTION_EXPORT_QTI = 'exportqti';
    const ACTION_IMPORT_QTI = 'importqti';
    const ACTION_SAVE_DOCUMENTS = 'save_documents';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_VIEW = 'view';
    const ACTION_DELETE_RESULTS = 'delete_results';
    const ACTION_MAIL_SURVEY_PARTICIPANTS = 'mail';
    
    const PARAM_USER_ASSESSMENT = 'uaid';
    const PARAM_QUESTION_ATTEMPT = 'qaid';
    const PARAM_ASSESMENT = 'aid';
    const PARAM_ANONYMOUS = 'anonymous';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_PUBLICATION_ACTION = 'publication_action';
    const PARAM_SURVEY_PARTICIPANT = 'survey_participant';

    /*
	 * Inherited.
	 */
    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_UP :
                $component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_DOWN :
                $component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Builder');
                break;
            case self :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Taker');
                break;
            case self :: ACTION_MAIL_SURVEY_PARTICIPANTS :
                $component = $this->create_component('Mailer');
                break;
            case self :: ACTION_SHOW_PUBLICATION :
                $component = $this->create_component('ShowPublication');
                break;
            case self :: ACTION_HIDE_PUBLICATION :
                $component = $this->create_component('HidePublication');
                break;
            case self :: ACTION_EDIT_RIGHTS :
                $component = $this->create_component('RightsEditor');
                break;
            default :
                $component = $this->create_component('Browser');
                break;
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Survey :: get_type_name());
    }

    //Url creation
    //	function get_create_survey_publication_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SURVEY_PUBLICATION));
    //    }
    //
    //    function get_update_survey_publication_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    //    function get_delete_survey_publication_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    function get_browse_survey_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW), array(self :: PARAM_PUBLICATION_ID, ComplexBuilder :: PARAM_BUILDER_ACTION));
    }

    //
    //    function get_browse_survey_pages_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGES, self :: PARAM_SURVEY => $survey_publication->get_content_object()));
    //    }
    //
    //    function get_browse_survey_page_questions_url($survey_page)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGE_QUESTIONS, self :: PARAM_SURVEY_PAGE => $survey_page->get_id()));
    //    }
    //
    //    function get_manage_survey_publication_categories_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES));
    //    }
    //
    //    function get_testcase_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TESTCASES), array(TestcaseManager :: PARAM_ACTION, TestcaseManager :: PARAM_SURVEY_PUBLICATION, ComplexBuilder :: PARAM_BUILDER_ACTION));
    //    }
    //
    function get_survey_publication_viewer_url($publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, self :: PARAM_PUBLICATION_ID => $publication->get_id()));
    }

    //
    //    function get_survey_results_viewer_url($survey_publication)
    //    {
    //        $id = $survey_publication ? $survey_publication->get_id() : null;
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS, self :: PARAM_SURVEY_PUBLICATION => $id));
    //    }
    //
    //    function get_reporting_survey_publication_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    //    function get_question_reporting_url($question)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_QUESTION_REPORTING, self :: PARAM_SURVEY_QUESTION => $question->get_id()));
    //    }
    //
    //    function get_import_survey_url()
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_SURVEY));
    //    }
    //
    //    function get_export_survey_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    //    function get_change_survey_publication_visibility_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    //    function get_move_survey_publication_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    //    function get_results_exporter_url($tracker_id)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
    //    }
    //
    //    function get_download_documents_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //
    function get_mail_survey_participant_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MAIL_SURVEY_PARTICIPANTS, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    //
    //    function get_build_survey_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    //    }
    //    
    

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }
}
?>