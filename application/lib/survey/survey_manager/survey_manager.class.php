<?php
/**
 * $Id: survey_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.survey.survey_manager
 */
require_once dirname(__FILE__) . '/survey_manager_component.class.php';
require_once dirname(__FILE__) . '/../survey_data_manager.class.php';
require_once dirname(__FILE__) . '/component/survey_publication_browser/survey_publication_browser_table.class.php';

/**
 * A survey manager
 *
 * @author Sven Vanpoucke
 * @author
 */
class SurveyManager extends WebApplication
{
    const APPLICATION_NAME = 'survey';
    
    const PARAM_SURVEY_PUBLICATION = 'survey_publication';
    const PARAM_SURVEY_PARTICIPANT = 'survey_participant';
    const PARAM_SURVEY_QUESTION = 'survey_question';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS = 'delete_selected_survey_publications';
    
    const ACTION_DELETE_SURVEY_PUBLICATION = 'delete';
    const ACTION_EDIT_SURVEY_PUBLICATION = 'edit';
    const ACTION_CREATE_SURVEY_PUBLICATION = 'create';
    const ACTION_BROWSE_SURVEY_PUBLICATIONS = 'browse';
    const ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES = 'manage_categories';
    const ACTION_BROWSE_TEST_SURVEY_PUBLICATION = 'browse_test';
    const ACTION_BROWSE_TEST_SURVEY_PARTICIPANTS = 'browse_participants';
    const ACTION_VIEW_SURVEY_PUBLICATION = 'view';
    const ACTION_VIEW_SURVEY_PUBLICATION_RESULTS = 'view_results';
    const ACTION_IMPORT_SURVEY = 'import_survey';
    const ACTION_EXPORT_SURVEY = 'export_survey';
    const ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY = 'change_visibility';
    const ACTION_MOVE_SURVEY_PUBLICATION = 'move';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_DOWNLOAD_DOCUMENTS = 'download_documents';
    const ACTION_PUBLISH_SURVEY = 'publish_survey';
    const ACTION_BUILD_SURVEY = 'build';

    /**
     * Constructor
     * @param User $user The current user
     */
    function SurveyManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    /**
     * Run this survey manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_SURVEY_PUBLICATIONS :
                $component = SurveyManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_BROWSE_TEST_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('TestBrowser', $this);
                break;
            case self :: ACTION_BROWSE_TEST_SURVEY_PARTICIPANTS :
                $component = SurveyManagerComponent :: factory('TestSurveyParticipantBrowser', $this);
                break;
            case self :: ACTION_DELETE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_EDIT_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Updater', $this);
                break;
            case self :: ACTION_CREATE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Creator', $this);
                break;
            case self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES :
                $component = SurveyManagerComponent :: factory('CategoryManager', $this);
                break;
            case self :: ACTION_VIEW_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS :
                $component = SurveyManagerComponent :: factory('ResultsViewer', $this);
                break;
            case self :: ACTION_IMPORT_SURVEY :
                $component = SurveyManagerComponent :: factory('SurveyImporter', $this);
                break;
            case self :: ACTION_EXPORT_SURVEY :
                $component = SurveyManagerComponent :: factory('SurveyExporter', $this);
                break;
            case self :: ACTION_CHANGE_SURVEY_PUBLICATION_VISIBILITY :
                $component = SurveyManagerComponent :: factory('VisibilityChanger', $this);
                break;
            case self :: ACTION_MOVE_SURVEY_PUBLICATION :
                $component = SurveyManagerComponent :: factory('Mover', $this);
                break;
            case self :: ACTION_EXPORT_RESULTS :
                $component = SurveyManagerComponent :: factory('ResultsExporter', $this);
                break;
            case self :: ACTION_DOWNLOAD_DOCUMENTS :
                $component = SurveyManagerComponent :: factory('DocumentDownloader', $this);
                break;
            case self :: ACTION_PUBLISH_SURVEY :
                $component = SurveyManagerComponent :: factory('SurveyPublisher', $this);
                break;
            case self :: ACTION_BUILD_SURVEY :
                $component = SurveyManagerComponent :: factory('Builder', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_SURVEY_PUBLICATIONS);
                $component = SurveyManagerComponent :: factory('Browser', $this);
        
        }
        $component->run();
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS :
                    
                    $selected_ids = $_POST[SurveyPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
                    
                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }
                    
                    $this->set_action(self :: ACTION_DELETE_SURVEY_PUBLICATION);
                    $_GET[self :: PARAM_SURVEY_PUBLICATION] = $selected_ids;
                    break;
            }
        
        }
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
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
    }

    function get_manage_survey_publication_categories_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_SURVEY_PUBLICATION_CATEGORIES));
    }

    function get_browse_test_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_TEST_SURVEY_PUBLICATION));
    }

    function get_browse_test_survey_participants_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_TEST_SURVEY_PARTICIPANTS, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_survey_publication_viewer_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_test_survey_publication_viewer_url($survey_participant)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION, self :: PARAM_SURVEY_PARTICIPANT => $survey_participant->get_id()));
    }
      
    function get_survey_results_viewer_url($survey_publication)
    {
        $id = $survey_publication ? $survey_publication->get_id() : null;
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_SURVEY_PUBLICATION_RESULTS, self :: PARAM_SURVEY_PUBLICATION => $id));
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

    function get_publish_survey_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function get_build_survey_url($survey_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_SURVEY, self :: PARAM_SURVEY_PUBLICATION => $survey_publication->get_id()));
    }

    function content_object_is_published($object_id)
    {
        return SurveyDataManager :: get_instance()->content_object_is_published($object_id);
    }

    function any_content_object_is_published($object_ids)
    {
        return SurveyDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return SurveyDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    function get_content_object_publication_attribute($publication_id)
    {
        return SurveyDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        return SurveyDataManager :: get_instance()->count_publication_attributes($type, $condition);
    }

    function delete_content_object_publications($object_id)
    {
        return SurveyDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    function delete_content_object_publication($publication_id)
    {
        return SurveyDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    function update_content_object_publication_id($publication_attr)
    {
        return SurveyDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array('survey', 'survey', 'hotpotatoes');
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('Surveys'));
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location)
    {
        $publication = new SurveyPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        $publication->set_hidden(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);
        
        $publication->create();
        return Translation :: get('PublicationCreated');
    }
}
?>