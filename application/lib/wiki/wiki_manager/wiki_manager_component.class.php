<?php
/**
 * $Id: wiki_manager_component.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager
 */
abstract class WikiManagerComponent
{
    /**
     * The number of components allready instantiated
     */
    private static $component_count = 0;
    
    /**
     * The wiki in which this componet is used
     */
    private $wiki;
    
    /**
     * The id of this component
     */
    private $id;
    
    private $rights;

    /**
     * Constructor
     * @param Wiki $wiki The wiki which
     * provides this component
     */
    protected function WikiManagerComponent($wiki)
    {
        $this->pm = $wiki;
        $this->id = ++ self :: $component_count;
        $this->load_rights();
    }

    /**
     * @see WikiManager :: redirect()
     */
    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
    }

    /**
     * @see WikiManager :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see WikiManager :: get_parameters()
     */
    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    /**
     * @see WikiManager :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        return $this->get_parent()->set_parameter($name, $value);
    }

    /**
     * @see WikiManager :: get_url()
     */
    function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
    }

    /**
     * @see WikiManager :: display_header()
     */
    function display_header($breadcrumbtrail, $display_search = false)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
    }

    /**
     * @see WikiManager :: display_message()
     */
    function display_message($message)
    {
        return $this->get_parent()->display_message($message);
    }

    /**
     * @see WikiManager :: display_error_message()
     */
    function display_error_message($message)
    {
        return $this->get_parent()->display_error_message($message);
    }

    /**
     * @see WikiManager :: display_warning_message()
     */
    function display_warning_message($message)
    {
        return $this->get_parent()->display_warning_message($message);
    }

    /**
     * @see WikiManager :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }

    /**
     * @see WikiManager :: display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see WikiManager :: display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see WikiManager :: display_popup_form
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * @see WikiManager :: get_parent
     */
    function get_parent()
    {
        return $this->pm;
    }

    /**
     * @see WikiManager :: get_web_code_path
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * @see WikiManager :: get_user()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see WikiManager :: get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    //Data Retrieval
    

    function count_wiki_publications($condition)
    {
        return $this->get_parent()->count_wiki_publications($condition);
    }

    function retrieve_wiki_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_wiki_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_wiki_publication($id)
    {
        return $this->get_parent()->retrieve_wiki_publication($id);
    }

    // Url Creation
    

    function get_create_wiki_publication_url()
    {
        return $this->get_parent()->get_create_wiki_publication_url();
    }

    function get_update_wiki_publication_url($wiki_publication)
    {
        return $this->get_parent()->get_update_wiki_publication_url($wiki_publication);
    }

    function get_delete_wiki_publication_url($wiki_publication)
    {
        return $this->get_parent()->get_delete_wiki_publication_url($wiki_publication);
    }

    function get_browse_wiki_publications_url()
    {
        return $this->get_parent()->get_browse_wiki_publications_url();
    }

    private function load_rights()
    {
        /**
         * Here we set the rights depending on the user status in the course.
         * This completely ignores the roles-rights library.
         * TODO: WORK NEEDED FOR PROPPER ROLES-RIGHTS LIBRARY
         */
        
        $this->rights[VIEW_RIGHT] = true;
        $this->rights[EDIT_RIGHT] = true;
        $this->rights[ADD_RIGHT] = true;
        $this->rights[DELETE_RIGHT] = true;
    }

    function is_allowed($right)
    {
        return $this->rights[$right];
    }

    /**
     * Create a new profile component
     * @param string $type The type of the component to create.
     * @param Profile $wiki The pm in
     * which the created component will be used
     */
    static function factory($type, $wiki)
    {
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'WikiManager' . $type . 'Component';
        require_once $filename;
        return new $class($wiki);
    }
}
?>