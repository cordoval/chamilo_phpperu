<?php
/**
 * $Id: assessment_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.assessment.assessment_manager
 */
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/component/assessment_publication_browser/assessment_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../assessment_rights.class.php';

/**
 * A assessment manager
 *
 * @author Sven Vanpoucke
 * @author
 */
class AssessmentManager extends WebApplication
{
    const APPLICATION_NAME = 'assessment';

    const PARAM_ASSESSMENT_PUBLICATION = 'assessment_publication';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_DELETE_SELECTED_ASSESSMENT_PUBLICATIONS = 'delete_selected_assessment_publications';

    const ACTION_DELETE_ASSESSMENT_PUBLICATION = 'delete';
    const ACTION_EDIT_ASSESSMENT_PUBLICATION = 'edit';
    const ACTION_CREATE_ASSESSMENT_PUBLICATION = 'create';
    const ACTION_BROWSE_ASSESSMENT_PUBLICATIONS = 'browse';
    const ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES = 'manage_categories';
    const ACTION_VIEW_ASSESSMENT_PUBLICATION = 'view';
    const ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS = 'view_results';
    const ACTION_IMPORT_QTI = 'import_qti';
    const ACTION_EXPORT_QTI = 'export_qti';
    const ACTION_CHANGE_ASSESSMENT_PUBLICATION_VISIBILITY = 'change_visibility';
    const ACTION_MOVE_ASSESSMENT_PUBLICATION = 'move';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_DOWNLOAD_DOCUMENTS = 'download_documents';
    const ACTION_PUBLISH_SURVEY = 'publish_survey';
    const ACTION_BUILD_ASSESSMENT = 'build';
    const ACTION_EDIT_RIGHTS = 'edit_rights';

    /**
     * Constructor
     * @param User $user The current user
     */
    function AssessmentManager($user = null)
    {
        parent :: __construct($user);
    }

    /**
     * Run this assessment manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_DELETE_ASSESSMENT_PUBLICATION :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_EDIT_ASSESSMENT_PUBLICATION :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_CREATE_ASSESSMENT_PUBLICATION :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_VIEW_ASSESSMENT_PUBLICATION :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS :
                $component = $this->create_component('ResultsViewer');
                break;
            case self :: ACTION_IMPORT_QTI :
                $component = $this->create_component('QtiImporter');
                break;
            case self :: ACTION_EXPORT_QTI :
                $component = $this->create_component('QtiExporter');
                break;
            case self :: ACTION_CHANGE_ASSESSMENT_PUBLICATION_VISIBILITY :
                $component = $this->create_component('VisibilityChanger');
                break;
            case self :: ACTION_MOVE_ASSESSMENT_PUBLICATION :
                $component = $this->create_component('Mover');
                break;
            case self :: ACTION_EXPORT_RESULTS :
                $component = $this->create_component('ResultsExporter');
                break;
            case self :: ACTION_DOWNLOAD_DOCUMENTS :
                $component = $this->create_component('DocumentDownloader');
                break;
            case self :: ACTION_PUBLISH_SURVEY :
                $component = $this->create_component('SurveyPublisher');
                break;
            case self :: ACTION_BUILD_ASSESSMENT :
            	$component = $this->create_component('Builder');
            	break;
            case self :: ACTION_EDIT_RIGHTS:
            	$component = $this->create_component('RightsEditor');
            	break;
            default :
                $this->set_action(self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS);
                $component = $this->create_component('Browser');

        }
        $component->run();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_assessment_publications($condition)
    {
        return AssessmentDataManager :: get_instance()->count_assessment_publications($condition);
    }

    function retrieve_assessment_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication($id)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publication($id);
    }

    function count_assessment_publication_groups($condition)
    {
        return AssessmentDataManager :: get_instance()->count_assessment_publication_groups($condition);
    }

    function retrieve_assessment_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication_group($id)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_group($id);
    }

    function count_assessment_publication_users($condition)
    {
        return AssessmentDataManager :: get_instance()->count_assessment_publication_users($condition);
    }

    function retrieve_assessment_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_assessment_publication_user($id)
    {
        return AssessmentDataManager :: get_instance()->retrieve_assessment_publication_user($id);
    }

    // Url Creation


    function get_create_assessment_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ASSESSMENT_PUBLICATION));
    }

    function get_update_assessment_publication_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ASSESSMENT_PUBLICATION, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_delete_assessment_publication_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ASSESSMENT_PUBLICATION, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_browse_assessment_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
    }

    function get_manage_assessment_publication_categories_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_ASSESSMENT_PUBLICATION_CATEGORIES));
    }

    function get_assessment_publication_viewer_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ASSESSMENT_PUBLICATION, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_assessment_results_viewer_url($assessment_publication)
    {
        $id = $assessment_publication ? $assessment_publication->get_id() : null;
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ASSESSMENT_PUBLICATION_RESULTS, self :: PARAM_ASSESSMENT_PUBLICATION => $id));
    }

    function get_import_qti_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_QTI));
    }

    function get_export_qti_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_QTI, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_change_assessment_publication_visibility_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_ASSESSMENT_PUBLICATION_VISIBILITY, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_move_assessment_publication_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_ASSESSMENT_PUBLICATION, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
    }

    function get_download_documents_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENTS, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_publish_survey_url($assessment_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH_SURVEY, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_build_assessment_url($assessment_publication)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_ASSESSMENT, self :: PARAM_ASSESSMENT_PUBLICATION => $assessment_publication->get_id()));
    }

    function get_rights_editor_url($category = null, $publication_ids = null)
    {
    	return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, self :: PARAM_ASSESSMENT_PUBLICATION => $publication_ids,
    								'category' => $category));
    }

    static function content_object_is_published($object_id)
    {
        return AssessmentDataManager :: get_instance()->content_object_is_published($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return AssessmentDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return AssessmentDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($publication_id)
    {
        return AssessmentDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

	static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return AssessmentDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return AssessmentDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

	static function delete_content_object_publication($publication_id)
    {
    	 return AssessmentDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return AssessmentDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(Assessment :: get_type_name(), Hotpotatoes :: get_type_name());

        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('Assessments'));
            return $locations;
        }

        return array();
    }

    static function publish_content_object($content_object, $location)
    {
        $publication = new AssessmentPublication();
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