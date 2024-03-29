<?php
namespace application\personal_messenger;

use common\libraries\ObjectTable;
use common\libraries\WebApplication;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Session;
/**
 * $Id: personal_messenger_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_messenger.personal_messenger_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A personal messenger manager allows a user to send/receive personal messages.
 * For each functionality a component is available.
 */
class PersonalMessengerManager extends WebApplication
{
    const APPLICATION_NAME = 'personal_messenger';

    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_MARK_SELECTED_READ = 'mark_selected_read';
    const PARAM_MARK_SELECTED_UNREAD = 'mark_selected_unread';
    const PARAM_FOLDER = 'folder';
    const PARAM_PERSONAL_MESSAGE_ID = 'pm';
    const PARAM_MARK_TYPE = 'type';
    const PARAM_USER_ID = 'user_id';

    const FOLDER_INBOX = 'inbox';
    const FOLDER_OUTBOX = 'outbox';

    const ACTION_DELETE_PUBLICATION = 'deleter';
    const ACTION_VIEW_PUBLICATION = 'viewer';
    const ACTION_VIEW_ATTACHMENTS = 'attachment_viewer';
    const ACTION_MARK_PUBLICATION = 'marker';
    const ACTION_CREATE_PUBLICATION = 'publisher';
    const ACTION_BROWSE_MESSAGES = 'browser';
    const ACTION_RIGHT_EDITS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_MESSAGES;

    const ACTION_RENDER_BLOCK = 'block';

    /**
     * Constructor
     * @param User $user The current user
     */
    function __construct($user = null)
    {
        parent :: __construct($user);

        $this->parse_input_from_table();

        $folder = Request :: get(self :: PARAM_FOLDER);
        if ($folder)
        {
            $this->set_parameter(self :: PARAM_FOLDER, $folder);
        }
        else
        {
            $this->set_parameter(self :: PARAM_FOLDER, self :: FOLDER_INBOX);
        }
    }

    function get_folder()
    {
        return $this->get_parameter(self :: PARAM_FOLDER);
    }

    /**
     * Renders the personal messenger block and returns it.
     */
    function render_block($block)
    {
        $personal_messenger_block = PersonalMessengerBlock :: factory($this, $block);
        return $personal_messenger_block->run();
    }

    public function has_menu()
    {
        return true;
    }

    /**
     * Displays the menu html
     */
    function get_menu()
    {
        $extra_items = array();
        $create = array();
        $create['title'] = Translation :: get('Send');
        $create['url'] = $this->get_personal_message_creation_url();
        $create['class'] = 'type_compose';
        $extra_items[] = $create;

        $temp_replacement = '__FOLDER__';
        $url_format = $this->get_url(array(
                Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES,
                PersonalMessengerManager :: PARAM_FOLDER => $temp_replacement));
        $url_format = str_replace($temp_replacement, '%s', $url_format);
        $user_menu = new PersonalMessengerMenu($this->get_folder(), $url_format, $extra_items);

        if ($this->get_action() == self :: ACTION_CREATE_PUBLICATION)
        {
            $user_menu->forceCurrentUrl($create['url'], true);
        }

        $html = array();
        $html[] = '<div style="float: left; width: 15%;">';
        $html[] = $user_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    /**
     * Sets the active URL in the navigation menu.
     * @param string $url The active URL.
     */
    function force_menu_url($url)
    {
        //$this->get_category_menu()->forceCurrentUrl($url);
    }

    /**
     * Returns whether a given object id is published in this application
     * @param int $object_id
     * @return boolean Is the object is published
     */
    static function content_object_is_published($object_id)
    {
        return PersonalMessengerDataManager :: get_instance()->content_object_is_published($object_id);
    }

    /**
     * Returns whether a given array of objects has been published
     * @param array $object_ids An array of object id's
     * @return boolean Was any learning object published
     */
    static function any_content_object_is_published($object_ids)
    {
        return PersonalMessengerDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    /**
     * Gets the publication attributes of a given array of learning object id's
     * @param array $object_id The array of object ids
     * @param string $type Type of retrieval
     * @param int $offset
     * @param int $count
     * @param int $order_property
     * @return array An array of Learing Object Publication Attributes
     */
    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return PersonalMessengerDataManager :: get_instance()->get_content_object_publication_attributes(Session :: get_user_id(), $object_id, $type, $offset, $count, $order_property);
    }

    /**
     * Gets the publication attributes of a given learning object id
     * @param int $object_id The object id
     * @return ContentObjectPublicationAttribute
     */
    static function get_content_object_publication_attribute($object_id)
    {
        return PersonalMessengerDataManager :: get_instance()->get_content_object_publication_attribute($object_id);
    }

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return int
     */
    static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PersonalMessengerDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return boolean
     */
    static function delete_content_object_publications($object_id)
    {
        return PersonalMessengerDataManager :: get_instance()->delete_personal_message_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return PersonalMessengerDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    /**
     * Update the publication id
     * @param ContentObjectPublicationAttribure $publication_attr
     * @return boolean
     */
    static function update_content_object_publication_id($publication_attr)
    {
        return PersonalMessengerDataManager :: get_instance()->update_personal_message_publication_id($publication_attr);
    }

    /**
     * Count the publications
     * @param Condition $condition
     * @return int
     */
    function count_personal_message_publications($condition = null)
    {
        $pmdm = PersonalMessengerDataManager :: get_instance();
        return $pmdm->count_personal_message_publications($condition);
    }

    /**
     * Count the unread publications
     * @return int
     */
    function count_unread_personal_message_publications()
    {
        $pmdm = PersonalMessengerDataManager :: get_instance();
        return $pmdm->count_unread_personal_message_publications($this->user);
    }

    /**
     * Retrieve a personal message publication
     * @param int $id
     * @return PersonalMessengerPublication
     */
    function retrieve_personal_message_publication($id)
    {
        $pmdm = PersonalMessengerDataManager :: get_instance();
        return $pmdm->retrieve_personal_message_publication($id);
    }

    /**
     * Retrieve a series of personal message publications
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return PersonalMessengerPublicationResultSet
     */
    function retrieve_personal_message_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $pmdm = PersonalMessengerDataManager :: get_instance();
        return $pmdm->retrieve_personal_message_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Inherited
     */
    static function get_content_object_publication_locations($content_object)
    {
        return array();
    }

    static function publish_content_object($content_object, $location)
    {

    }

    /**
     * Gets the url for deleting a personal message publication
     * @param PersonalMessengerPublication
     * @return string The url
     */
    function get_publication_deleting_url($personal_message)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION,
                self :: PARAM_PERSONAL_MESSAGE_ID => $personal_message->get_id()));
    }

    /**
     * Gets the url for viewing a personal message publication
     * @param PersonalMessengerPublication
     * @return string The url
     */
    function get_publication_viewing_url($personal_message)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION,
                self :: PARAM_PERSONAL_MESSAGE_ID => $personal_message->get_id()));
    }

    function get_publication_viewing_link($personal_message)
    {
        return $this->get_link(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION,
                self :: PARAM_PERSONAL_MESSAGE_ID => $personal_message->get_id(),
                self :: PARAM_FOLDER => $this->get_folder()));
    }

    /**
     * Gets the url for creating a personal message publication
     * @param PersonalMessengerPublication
     * @return string The url
     */
    function get_personal_message_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PUBLICATION));
    }

    function get_publication_reply_url($personal_message)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_CREATE_PUBLICATION,
                'reply' => $personal_message->get_id(),
                PersonalMessengerManager :: PARAM_USER_ID => $personal_message->get_sender()));
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[PmPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_MARK_SELECTED_READ :
                    $this->set_action(self :: ACTION_MARK_PUBLICATION);
                    Request :: set_get(self :: PARAM_PERSONAL_MESSAGE_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_MARK_TYPE, self :: PARAM_MARK_SELECTED_READ);
                    break;
                case self :: PARAM_MARK_SELECTED_UNREAD :
                    $this->set_action(self :: ACTION_MARK_PUBLICATION);
                    Request :: set_get(self :: PARAM_PERSONAL_MESSAGE_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_MARK_TYPE, self :: PARAM_MARK_SELECTED_UNREAD);
                    break;
                case self :: PARAM_DELETE_SELECTED :
                    $this->set_action(self :: ACTION_DELETE_PUBLICATION);
                    Request :: set_get(self :: PARAM_PERSONAL_MESSAGE_ID, $selected_ids);
                    break;
            }
        }
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
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