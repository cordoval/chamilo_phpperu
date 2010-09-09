<?php
/**
 * $Id: forum_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.forum.forum_manager
 */
require_once dirname(__FILE__) . '/../forum_data_manager.class.php';
require_once dirname(__FILE__) . '/../forum_rights.class.php';

/**
 * A forum manager
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumManager extends WebApplication
{
    const APPLICATION_NAME = 'forum';

    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_MOVE = 'move';

    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT = 'editor';
    const ACTION_CREATE = 'creator';
    const ACTION_VIEW = 'viewer';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_BROWSE = 'browser';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_MOVE = 'mover';
    const ACTION_MANAGE_CATEGORIES = 'category_manager';
    const ACTION_EVALUATE = 'forum_evaluation';
    const ACTION_EDIT_RIGHTS = 'rights_editor';
	const ACTION_CHANGE_LOCK = 'change_lock';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    private $parameters;
    private $user;
    private $rights;

    /**
     * Constructor
     * @param User $user The current user
     */
    function ForumManager($user = null)
    {
        $this->user = $user;
        $this->parameters = array();
        $this->load_rights();
        $this->set_action(Request :: get(self :: PARAM_ACTION));
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_forum_publications($condition)
    {
        return ForumDataManager :: get_instance()->count_forum_publications($condition);
    }

    function retrieve_forum_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return ForumDataManager :: get_instance()->retrieve_forum_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_forum_publication($id)
    {
        return ForumDataManager :: get_instance()->retrieve_forum_publication($id);
    }

    // Url Creation


    function get_create_forum_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_FORUM_PUBLICATION));
    }

    function get_update_forum_publication_url($forum_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_FORUM_PUBLICATION, self :: PARAM_PUBLICATION_ID => $forum_publication->get_id()));
    }

    function get_delete_forum_publication_url($forum_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_FORUM_PUBLICATION, self :: PPARAM_PUBLICATION_ID => $forum_publication->get_id()));
    }

    function get_browse_forum_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES));
    }

    function get_rights_editor_url($category = null)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, 'category' => $category));
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return ForumDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    static function get_content_object_publication_attribute($object_id)
    {
        return ForumDataManager :: get_instance()->get_content_object_publication_attribute($object_id);
    }

    static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return ForumDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    function get_user_id()
    {
        return $this->user->get_id();
    }

    function is_allowed($right)
    {
        return $this->rights[$right];
    }

    function get_user()
    {
        return $this->user;
    }

    /**
     * Load the rights for the current user in this tool
     */
    private function load_rights()
    {
        $this->rights[VIEW_RIGHT] = true;
        $this->rights[EDIT_RIGHT] = true;
        $this->rights[ADD_RIGHT] = true;
        $this->rights[DELETE_RIGHT] = true;
        //		$user = $this->user;
        //		if ($user != null)
        //		{
        //			if($user->is_platform_admin())
        //			{
        //				$this->rights[EDIT_RIGHT] = true;
        //				$this->rights[ADD_RIGHT] = true;
        //				$this->rights[DELETE_RIGHT] = true;
        //			}
        //		}
        return;
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