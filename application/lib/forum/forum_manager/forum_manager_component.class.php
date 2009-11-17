<?php
/**
 * $Id: forum_manager_component.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager
 */
/**
 * Basic functionality of a component to talk with the forum application
 * @author Sven Vanpoucke & Michael Kyndt
 */
abstract class ForumManagerComponent
{
    /**
     * The number of components allready instantiated
     */
    private static $component_count = 0;
    
    /**
     * The forum in which this componet is used
     */
    private $forum;
    
    /**
     * The id of this component
     */
    private $id;

    /**
     * Constructor
     * @param Forum $forum The forum which
     * provides this component
     */
    protected function ForumManagerComponent($forum)
    {
        $this->pm = $forum;
        $this->id = ++ self :: $component_count;
    }

    /**
     * @see ForumManager :: redirect()
     */
    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
    }

    /**
     * @see ForumManager :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see ForumManager :: get_parameters()
     */
    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    /**
     * @see ForumManager :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        return $this->get_parent()->set_parameter($name, $value);
    }

    /**
     * @see ForumManager :: get_url()
     */
    function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
    }

    /**
     * @see ForumManager :: display_header()
     */
    function display_header($breadcrumbtrail, $display_search = false)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
    }

    /**
     * @see ForumManager :: display_message()
     */
    function display_message($message)
    {
        return $this->get_parent()->display_message($message);
    }

    /**
     * @see ForumManager :: display_error_message()
     */
    function display_error_message($message)
    {
        return $this->get_parent()->display_error_message($message);
    }

    /**
     * @see ForumManager :: display_warning_message()
     */
    function display_warning_message($message)
    {
        return $this->get_parent()->display_warning_message($message);
    }

    /**
     * @see ForumManager :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }

    /**
     * @see ForumManager :: display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see ForumManager :: display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see ForumManager :: display_popup_form
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * @see ForumManager :: get_parent
     */
    function get_parent()
    {
        return $this->pm;
    }

    /**
     * @see ForumManager :: get_web_code_path
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * @see ForumManager :: get_user()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see ForumManager :: get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    //Data Retrieval
    

    function count_forum_publications($condition)
    {
        return $this->get_parent()->count_forum_publications($condition);
    }

    function retrieve_forum_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_forum_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_forum_publication($id)
    {
        return $this->get_parent()->retrieve_forum_publication($id);
    }

    // Url Creation
    

    function get_create_forum_publication_url()
    {
        return $this->get_parent()->get_create_forum_publication_url();
    }

    function get_update_forum_publication_url($forum_publication)
    {
        return $this->get_parent()->get_update_forum_publication_url($forum_publication);
    }

    function get_delete_forum_publication_url($forum_publication)
    {
        return $this->get_parent()->get_delete_forum_publication_url($forum_publication);
    }

    function get_browse_forum_publications_url()
    {
        return $this->get_parent()->get_browse_forum_publications_url();
    }

    function get_category_manager_url()
    {
        return $this->get_parent()->get_category_manager_url();
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    /**
     * Create a new profile component
     * @param string $type The type of the component to create.
     * @param Profile $forum The pm in
     * which the created component will be used
     */
    static function factory($type, $forum)
    {
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'ForumManager' . $type . 'Component';
        require_once $filename;
        return new $class($forum);
    }
}
?>