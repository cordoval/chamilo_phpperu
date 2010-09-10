<?php

require_once dirname(__FILE__) . '/../survey_data_manager.class.php';

require_once Path :: get_application_path() . 'lib/survey/survey_rights.class.php';
//require_once Path :: get_application_path() . 'lib/survey/testcase_manager/testcase_manager.class.php';


require_once dirname(__FILE__) . '/component/survey_publication_browser/survey_publication_browser_table.class.php';

class SurveyManager extends WebApplication
{
    
    const APPLICATION_NAME = 'survey';
    
    const PARAM_SURVEY_PUBLICATION = 'survey_publication';
    const PARAM_SURVEY = 'survey';
    const PARAM_SURVEY_PARTICIPANT = 'survey_participant';
    const PARAM_SURVEY_INVITEE = 'survey_invitee';
    const PARAM_SURVEY_INVITEES = 'survey_invitees';
    
    const PARAM_SURVEY_PAGE = 'survey_page';
    const PARAM_SURVEY_QUESTION = 'survey_question';
    const PARAM_MAIL_PARTICIPANTS = 'mail_participant';
    const PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS = 'delete_selected_survey_publications';
    
    //    const PARAM_TESTCASE = 'testcase';
    

    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    
    const ACTION_DELETE_SURVEY_PUBLICATION = 'deleter';
    const ACTION_EDIT_SURVEY_PUBLICATION_RIGHTS = 'rights_editor';
    const ACTION_EDIT_SURVEY_PUBLICATION = 'updater';
    const ACTION_CREATE_SURVEY_PUBLICATION = 'creator';
    const ACTION_BROWSE_SURVEY_PUBLICATIONS = 'browser';
    const ACTION_BROWSE_SURVEY_PAGES = 'page_browser';
    const ACTION_BROWSE_SURVEY_PAGE_QUESTIONS = 'question_browser';
    const ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES = 'category_manager';
    const ACTION_VIEW_SURVEY_PUBLICATION = 'viewer';
    const ACTION_VIEW_SURVEY_PUBLICATION_RESULTS = 'results_viewer';
    const ACTION_REPORTING_FILTER = 'reporting_filter';
    const ACTION_REPORTING = 'reporting';
    const ACTION_EXCEL_EXPORT = 'survey_spss_syntax_exporter';
    const ACTION_QUESTION_REPORTING = 'question_reporting';
    
    const ACTION_BROWSE_SURVEY_PARTICIPANTS = 'participant_browser';
    const ACTION_BROWSE_SURVEY_EXCLUDED_USERS = 'user_browser';
    const ACTION_CANCEL_INVITATION = 'invitation_canceler';
    
    //    const ACTION_CHANGE_TEST_TO_PRODUCTION = 'changer';
    

    const ACTION_IMPORT_SURVEY = 'survey_importer';
    const ACTION_EXPORT_SURVEY = 'survey_exporter';
    const ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY = 'visibility_changer';
    const ACTION_MOVE_SURVEY_PUBLICATION = 'mover';
    const ACTION_EXPORT_RESULTS = 'results_exporter';
    const ACTION_DOWNLOAD_DOCUMENTS = 'document_downloader';
    
    const ACTION_MAIL_SURVEY_PARTICIPANTS = 'mailer';
    const ACTION_INVITE_EXTERNAL_USERS = 'inviter';
    
    const ACTION_BUILD_SURVEY = 'builder';
    //    const ACTION_TESTCASES = 'testcase';
    

    const DEFAULT_ACTION = self :: ACTION_BROWSE_SURVEY_PUBLICATIONS;

    /**
     * Constructor
     * @param User $user The current user
     */
    function SurveyManager($user = null)
    {
        parent :: __construct($user);
        //$this->parse_input_from_table();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving
    

    function count_survey_participant_trackers($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_participant_trackers($condition);
    }

    function retrieve_survey_participant_trackers($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_participant_trackers($condition, $offset, $count, $order_property);
    }

    function count_survey_publications($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_publications($condition);
    }

    function retrieve_survey_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_survey_publication($id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication($id);
    }

    function count_survey_publication_groups($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_publication_groups($condition);
    }

    function retrieve_survey_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_survey_publication_group($id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_group($id);
    }

    function count_survey_publication_users($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_publication_users($condition);
    }

    function retrieve_survey_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_survey_publication_user($id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_user($id);
    }

    function count_survey_publication_mails($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_publication_mails($condition);
    }

    function retrieve_survey_publication_mail($id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_mail($id);
    }

    function retrieve_survey_publication_mails($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_publication_mails($condition, $offset, $count, $order_property);
    }

    function count_survey_pages($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_pages($condition);
    }

    function retrieve_survey_pages($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_pages($condition, $offset, $count, $order_property);
    }

    function retrieve_survey_page($page_id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_page($page_id);
    }

    function count_survey_questions($condition)
    {
        return SurveyDataManager :: get_instance()->count_survey_questions($condition);
    }

    function retrieve_survey_question($question_id)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_question($question_id);
    }

    function retrieve_survey_questions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->retrieve_survey_questions($condition, $offset, $count, $order_property);
    }

    // Url Creation
    

    function get_create_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SURVEY_PUBLICATION));
    }

    function get_update_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_delete_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_browse_survey_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS), array(self :: PARAM_SURVEY_PUBLICATION, ComplexBuilder :: PARAM_BUILDER_ACTION));
    }

    function get_browse_survey_pages_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGES, self :: PARAM_SURVEY => $survey_publication->get_content_object()));
    }

    function get_browse_survey_page_questions_url($survey_page)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PAGE_QUESTIONS, self :: PARAM_SURVEY_PAGE => $survey_page->get_id()));
    }

    function get_manage_survey_publication_categories_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES));
    }

//    function get_testcase_url()
//    {
//        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TESTCASES), array(TestcaseManager :: PARAM_ACTION, TestcaseManager :: PARAM_SURVEY_PUBLICATION, ComplexBuilder :: PARAM_BUILDER_ACTION));
//    }

    function get_survey_publication_viewer_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_results_viewer_url($survey_publication)
    {
        $id = $survey_publication ? $survey_publication->get_id() : null;
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS, self :: PARAM_SURVEY_PUBLICATION => $id));
    }

    function get_reporting_filter_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING_FILTER));
    }

    function get_reporting_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_question_reporting_url($question)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_QUESTION_REPORTING, self :: PARAM_SURVEY_QUESTION => $question->get_id()));
    }

    function get_import_survey_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_SURVEY));
    }

    function get_export_survey_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_change_survey_publication_visibility_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_move_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
    }

    function get_download_documents_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_mail_survey_participant_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MAIL_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_build_survey_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_publication_export_excel_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXCEL_EXPORT, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_browse_survey_excluded_users_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_EXCLUDED_USERS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_change_test_to_production_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_TEST_TO_PRODUCTION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_participant_publication_viewer_url($survey_participant_tracker)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION, SurveyManager :: PARAM_SURVEY_PUBLICATION => $survey_participant_tracker->get_survey_publication_id(), SurveyManager :: PARAM_SURVEY_PARTICIPANT => $survey_participant_tracker->get_id()));
    }

	function get_survey_invitee_publication_viewer_url($publication_id, $user_id)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_VIEW_SURVEY_PUBLICATION, SurveyManager :: PARAM_SURVEY_PUBLICATION => $publication_id, SurveyManager :: PARAM_SURVEY_INVITEE => $user_id));
    }
    
    function get_rights_editor_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_SURVEY_PUBLICATION_RIGHTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    
    }

function get_survey_cancel_invitation_url($survey_publication_id, $invitee)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_CANCEL_INVITATION, SurveyManager :: PARAM_SURVEY_INVITEES => $survey_publication_id .'|'.$invitee));
    }
    
    //publications
    

    static function content_object_is_published($object_id)
    {
        return SurveyDataManager :: get_instance()->content_object_is_published($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return SurveyDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($publication_id)
    {
        return SurveyDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    static function count_publication_attributes($type = null, $condition = null)
    {
        return SurveyDataManager :: get_instance()->count_publication_attributes($type, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return SurveyDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return SurveyDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return SurveyDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function add_publication_attributes_elements($form)
    {
        $form->addElement('category', Translation :: get('PublicationDetails'));
        $form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
        $form->add_select(self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_TYPE, Translation :: get('SurveyType'), SurveyPublication :: get_types());
        $form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $form->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $form->addElement('category');
        $form->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $form->setDefaults($defaults);
    }

    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(Survey :: get_type_name());
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            //            $categories = SurveyDataManager :: get_instance()->retrieve_survey_publication_categories();
            $locations = array();
            //            while ($category = $categories->next_result())
            //            {
            //            $locations[$category->get_id()] = $category->get_name() . ' - category';
            //            }
            //            $locations[0] = Translation :: get('RootSurveyCategory');
            

            $locations[1] = Translation :: get('SurveyApplication');
            return $locations;
        }
        
        return array();
    }

    static function publish_content_object($content_object, $location, $attributes)
    {
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_ADD, SurveyRights :: LOCATION_BROWSER, SurveyRights :: TYPE_SURVEY_COMPONENT))
        {
            return Translation :: get('NoRightsForSurveyPublication');
        }
        
        $publication = new SurveyPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        //        $publication->set_category($location);
        

        if ($attributes[SurveyPublication :: PROPERTY_HIDDEN] == 1)
        {
            $publication->set_hidden(1);
        }
        else
        {
            $publication->set_hidden(0);
        }
        
        if ($attributes['forever'] == 1)
        {
            $publication->set_from_date(0);
            $publication->set_to_date(0);
        }
        else
        {
            $publication->set_from_date(Utilities :: time_from_datepicker($attributes['from_date']));
            $publication->set_to_date(Utilities :: time_from_datepicker($attributes['to_date']));
        }
        
        //        if ($attributes[SurveyPublication :: PROPERTY_TYPE] == 1)
        //        {
        

        $publication->set_type($attributes[SurveyPublication :: PROPERTY_TYPE]);
        //        }
        //        else
        //        {
        //            $publication->set_test(0);
        //        }
        

        if ($attributes[self :: PARAM_TARGET_OPTION] != 0)
        {
            $user_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['user'];
            $group_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['group'];
        }
        else
        {
            $users = UserDataManager :: get_instance()->retrieve_users();
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
        }
        
//        $publication->set_target_users($user_ids);
//        $publication->set_target_groups($group_ids);
        
        $publication->create();
        
        $locations[] = SurveyRights :: get_location_by_identifier_from_surveys_subtree($publication->get_id(), SurveyRights :: TYPE_PUBLICATION);
               
        foreach ($locations as $location)
        {
            foreach ($user_ids as $user_id)
            {
                RightsUtilities :: set_user_right_location_value(SurveyRights :: RIGHT_VIEW, $user_id, $location->get_id(), 1);
            }
            foreach ($group_ids as $group_id)
            {
                RightsUtilities :: set_group_right_location_value(SurveyRights :: RIGHT_VIEW, $group_id, $location->get_id(), 1);
            }
        }
        
        return Translation :: get('PublicationCreated');
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>