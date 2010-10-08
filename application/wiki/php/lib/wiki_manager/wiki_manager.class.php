<?php
/**
 * $Id: wiki_manager.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager
 */
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_manager/component/wiki_publication_browser/wiki_publication_browser_table.class.php';

/**
 * A wiki manager
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManager extends WebApplication
{
    const APPLICATION_NAME = 'wiki';

    const PARAM_WIKI_PUBLICATION = 'wiki_publication';
    const PARAM_DELETE_SELECTED_WIKI_PUBLICATIONS = 'delete_selected_wiki_publications';

    const ACTION_DELETE_WIKI_PUBLICATION = 'wiki_publication_deleter';
    const ACTION_EDIT_WIKI_PUBLICATION = 'wiki_publication_updater';
    const ACTION_CREATE_WIKI_PUBLICATION = 'wiki_publication_creator';
    const ACTION_BROWSE_WIKI_PUBLICATIONS = 'wiki_publications_browser';
    const ACTION_VIEW_WIKI = 'wiki_viewer';
    const ACTION_EVALUATE_WIKI_PUBLICATION = 'wiki_evaluation';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_WIKI_PUBLICATIONS;

    /**
     * Constructor
     * @param User $user The current user
     */
    function WikiManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_WIKI_PUBLICATIONS :

                    $selected_ids = $_POST[WikiPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_WIKI_PUBLICATION);
                    $_GET[self :: PARAM_WIKI_PUBLICATION] = $selected_ids;
                    break;
            }

        }
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_wiki_publications($condition)
    {
        return WikiDataManager :: get_instance()->count_wiki_publications($condition);
    }

    function retrieve_wiki_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return WikiDataManager :: get_instance()->retrieve_wiki_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_wiki_publication($id)
    {
        return WikiDataManager :: get_instance()->retrieve_wiki_publication($id);
    }

    // Url Creation


    function get_create_wiki_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_WIKI_PUBLICATION));
    }

    function get_evaluation_publication_url($wiki_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EVALUATE_WIKI_PUBLICATION, self :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
    }

    function get_update_wiki_publication_url($wiki_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_WIKI_PUBLICATION, self :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
    }

    function get_delete_wiki_publication_url($wiki_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_WIKI_PUBLICATION, self :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id()));
    }

    function get_browse_wiki_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_WIKI_PUBLICATIONS));
    }

    function is_allowed()
    {
        return true;
    }

    // Dummy Methods which are needed because we don't work with learning objects
    static function content_object_is_published($object_id)
    {
        return WikiDataManager :: get_instance()->content_object_is_publish($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return WikiDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return WikiDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($object_id)
    {
        return WikiDataManager :: get_instance()->get_content_object_publication_attribute($object_id);
    }

    static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return WikiDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return WikiDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return WikiDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return WikiDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(Wiki :: get_type_name());

        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(Translation :: get('Wiki'));
            return $locations;
        }

        return array();
    }

    static function publish_content_object($content_object, $location)
    {
        $publication = new WikiPublication();
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