<?php

require_once Path :: get_application_path() . 'lib/survey/survey_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/survey/survey_rights.class.php';
//require_once Path :: get_application_path() . 'lib/survey/survey_manager/component/publication_browser/publication_browser_table.class.php';
//require_once Path :: get_application_path() . 'lib/survey/survey_manager/component/participant_browser/participant_browser_table.class.php';
//require_once Path :: get_application_path() . 'lib/survey/survey_manager/component/user_browser/user_browser_table.class.php';


class SurveyManager extends WebApplication
{
    
    const APPLICATION_NAME = 'survey';
    
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_PARTICIPANT_ID = 'participant_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    
    const PARAM_SURVEY_PAGE_ID = 'page_id';
    const PARAM_SURVEY_QUESTION_ID = 'question_id';
    const PARAM_MAIL_ID = 'mail_id';
    
    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT_RIGHTS = 'rights_editor';
    const ACTION_EDIT = 'editor';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_BROWSE = 'browser';
    const ACTION_BROWSE_PAGES = 'page_browser';
    const ACTION_BROWSE_PAGE_QUESTIONS = 'question_browser';
    const ACTION_TAKE = 'taker';
    const ACTION_REPORTING_FILTER = 'reporting_filter';
    const ACTION_REPORTING = 'reporting_filter';
    const ACTION_EXCEL_EXPORT = 'survey_excel_median_exporter';
    const ACTION_QUESTION_REPORTING = 'question_reporting';
    
    const ACTION_SUBSCRIBE_USER = 'subscribe_user';
    const ACTION_SUBSCRIBE_GROUP = 'subscribe_group';
    
    const ACTION_BROWSE_PARTICIPANTS = 'participant_browser';
    const ACTION_CANCEL_INVITATION = 'invitation_canceler';
    const ACTION_EXPORT_RESULTS = 'results_exporter';
    const ACTION_MAIL_INVITEES = 'mailer';
    const ACTION_INVITE_EXTERNAL_USERS = 'inviter';
    
    //we don't allow to go to the builder of the survey from the publication: building surveys
    //only in the repository ==> just for usability ==> this has to be evaluated !
    const ACTION_BUILD_SURVEY = 'builder';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Constructor
     * @param User $user The current user
     */
    function SurveyManager($user = null)
    {
        parent :: __construct($user);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Url Creation
    

    function get_create_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH));
    }

    function get_update_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_delete_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_browse_survey_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE), array(self :: PARAM_PUBLICATION_ID, ComplexBuilder :: PARAM_BUILDER_ACTION));
    }

    function get_browse_survey_pages_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PAGES, self :: PARAM_SURVEY_ID => $survey_publication->get_content_object()));
    }

    function get_browse_survey_page_questions_url($survey_page)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PAGE_QUESTIONS, self :: PARAM_SURVEY_PAGE_ID => $survey_page->get_id()));
    }

    function get_survey_publication_viewer_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_TAKE, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id(), self :: PARAM_SURVEY_ID => $survey_publication->get_content_object(), self :: PARAM_INVITEE_ID => $this->get_user_id()));
    }

    function get_reporting_filter_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING_FILTER));
    }

    function get_reporting_survey_publication_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_question_reporting_url($question)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_QUESTION_REPORTING, self :: PARAM_SURVEY_QUESTION_ID => $question->get_id()));
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
    }

    function get_mail_survey_participant_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MAIL_INVITEES, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    //link to builder has to be evaluated
    //    function get_build_survey_url($survey_publication)
    //    {
    //        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_SURVEY, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    //    }
    

    function get_survey_publication_export_excel_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXCEL_EXPORT, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_survey_participant_publication_viewer_url($survey_participant_tracker)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_TAKE, SurveyManager :: PARAM_PUBLICATION_ID => $survey_participant_tracker->get_survey_publication_id(), SurveyManager :: PARAM_PARTICIPANT_ID => $survey_participant_tracker->get_id()));
    }

    function get_survey_invitee_publication_viewer_url($publication_id, $user_id)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_TAKE, SurveyManager :: PARAM_PUBLICATION_ID => $publication_id, SurveyManager :: PARAM_INVITEE_ID => $user_id));
    }

    function get_rights_editor_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    
    }

    function get_survey_cancel_invitation_url($survey_publication_id, $invitee)
    {
        return $this->get_url(array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_CANCEL_INVITATION, SurveyManager :: PARAM_INVITEE_ID => $survey_publication_id . '|' . $invitee));
    }

    function get_subscribe_user_url($publication_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER, self :: PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_subscribe_group_url($publication_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_GROUP, self :: PARAM_PUBLICATION_ID => $publication_id));
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
        //no publication from repository to tool:: has to be evaluated
        

        //        $allowed_types = array(Survey :: get_type_name());
        //        
        //        $type = $content_object->get_type();
        //        if (in_array($type, $allowed_types))
        //        {
        //            //            $categories = SurveyDataManager :: get_instance()->retrieve_survey_publication_categories();
        //            $locations = array();
        //            //            while ($category = $categories->next_result())
        //            //            {
        //            //            $locations[$category->get_id()] = $category->get_name() . ' - category';
        //            //            }
        //            //            $locations[0] = Translation :: get('RootSurveyCategory');
        //            
        //
        //            $locations[1] = Translation :: get('SurveyApplication');
        //            return $locations;
        //        }
        

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
        
        $publication->set_type($attributes[SurveyPublication :: PROPERTY_TYPE]);
        
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

    static public function __autoload($classname)
    {
        $list = array('survey_publication_browser_table' => 'component/publication_browser/publication_browser_table.class.php', 'survey_participant_browser_table' => 'component/participant_browser/participant_browser_table.class.php', 'survey_user_browser_table' => 'component/user_browser/user_browser_table.class.php');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/' . $url;
            return true;
        }
        
        return false;
    }
}
?>