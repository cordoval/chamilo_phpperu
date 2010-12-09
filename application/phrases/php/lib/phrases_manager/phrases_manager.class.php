<?php
namespace application\phrases;

use common\libraries\WebApplication;
use repository\content_object\adaptive_assessment\AdaptiveAssessment;
use common\libraries\Translation;
use common\libraries\Session;
/**
 * $Id: phrases_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.phrases.phrases_manager
 */
require_once dirname(__FILE__) . '/../phrases_data_manager.class.php';
require_once dirname(__FILE__) . '/component/phrases_publication_browser/phrases_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../phrases_rights.class.php';

/**
 * A phrases manager
 *
 * @author Hans De Bisschop
 * @author
 */
class PhrasesManager extends WebApplication
{
    const APPLICATION_NAME = 'phrases';

    const PARAM_PHRASES_PUBLICATION = 'phrases_publication';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_DELETE_SELECTED_PHRASES_PUBLICATIONS = 'delete_selected_phrases_publications';
    const PARAM_CATEGORY = 'category';

    const ACTION_DELETE_PHRASES_PUBLICATION = 'deleter';
    const ACTION_EDIT_PHRASES_PUBLICATION = 'updater';
    const ACTION_CREATE_PHRASES_PUBLICATION = 'creator';
    const ACTION_BROWSE_PHRASES_PUBLICATIONS = 'browser';
    const ACTION_MANAGE_PHRASES_PUBLICATION_CATEGORIES = 'category_manager';
    const ACTION_VIEW_PHRASES_PUBLICATION = 'viewer';
    const ACTION_VIEW_PHRASES_PUBLICATION_RESULTS = 'results_viewer';
    const ACTION_IMPORT_QTI = 'qti_importer';
    const ACTION_EXPORT_QTI = 'qti_exporter';
    const ACTION_CHANGE_PHRASES_PUBLICATION_VISIBILITY = 'visibility_changer';
    const ACTION_MOVE_PHRASES_PUBLICATION = 'mover';
    const ACTION_EXPORT_RESULTS = 'results_exporter';
    const ACTION_DOWNLOAD_DOCUMENTS = 'document_downloader';
    const ACTION_PUBLISH_SURVEY = 'survey_publisher';
    const ACTION_BUILD_PHRASES = 'builder';
    const ACTION_EDIT_RIGHTS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_PHRASES_PUBLICATIONS;

    /**
     * Constructor
     * @param User $user The current user
     */
    function __construct($user = null)
    {
        parent :: __construct($user);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_phrases_publications($condition)
    {
        return PhrasesDataManager :: get_instance()->count_phrases_publications($condition);
    }

    function retrieve_phrases_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_phrases_publication($id)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publication($id);
    }

    function count_phrases_publication_groups($condition)
    {
        return PhrasesDataManager :: get_instance()->count_phrases_publication_groups($condition);
    }

    function retrieve_phrases_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_phrases_publication_group($id)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publication_group($id);
    }

    function count_phrases_publication_users($condition)
    {
        return PhrasesDataManager :: get_instance()->count_phrases_publication_users($condition);
    }

    function retrieve_phrases_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_phrases_publication_user($id)
    {
        return PhrasesDataManager :: get_instance()->retrieve_phrases_publication_user($id);
    }

    // Url Creation


    function get_create_phrases_publication_url()
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_CREATE_PHRASES_PUBLICATION));
    }

    function get_update_phrases_publication_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_PHRASES_PUBLICATION,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_delete_phrases_publication_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_PHRASES_PUBLICATION,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_browse_phrases_publications_url()
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
    }

    function get_manage_phrases_publication_categories_url()
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_PHRASES_PUBLICATION_CATEGORIES));
    }

    function get_publication_viewer_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_PHRASES_PUBLICATION,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_phrases_results_viewer_url($phrases_publication)
    {
        $id = $phrases_publication ? $phrases_publication->get_id() : null;
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_PHRASES_PUBLICATION_RESULTS,
                self :: PARAM_PHRASES_PUBLICATION => $id));
    }

    function get_import_qti_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_QTI));
    }

    function get_export_qti_url($phrases_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_QTI,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_change_phrases_publication_visibility_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_CHANGE_PHRASES_PUBLICATION_VISIBILITY,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_move_phrases_publication_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_PHRASES_PUBLICATION,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS,
                'tid' => $tracker_id));
    }

    function get_download_documents_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DOWNLOAD_DOCUMENTS,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_publish_survey_url($phrases_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_PUBLISH_SURVEY,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_build_phrases_url($phrases_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_PHRASES,
                self :: PARAM_PHRASES_PUBLICATION => $phrases_publication->get_id()));
    }

    function get_rights_editor_url($category = null, $publication_ids = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS,
                self :: PARAM_PHRASES_PUBLICATION => $publication_ids,
                'category' => $category));
    }

    static function content_object_is_published($object_id)
    {
        return PhrasesDataManager :: get_instance()->content_object_is_published($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return PhrasesDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return PhrasesDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($publication_id)
    {
        return PhrasesDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PhrasesDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return PhrasesDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return PhrasesDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return PhrasesDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function get_content_object_publication_locations($content_object)
    {
        return array();
    }

    static function publish_content_object($content_object, $location)
    {
        $publication = new PhrasesPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        $publication->set_hidden(0);
        $publication->set_from_date(0);
        $publication->set_to_date(0);

        $publication->create();
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