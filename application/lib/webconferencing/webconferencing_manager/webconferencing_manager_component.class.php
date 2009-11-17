<?php
/**
 * $Id: webconferencing_manager_component.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.webconferencing_manager
 */
abstract class WebconferencingManagerComponent
{
    /**
     * The number of components allready instantiated
     */
    private static $component_count = 0;
    
    /**
     * The webconferencing in which this componet is used
     */
    private $webconferencing;
    
    /**
     * The id of this component
     */
    private $id;

    /**
     * Constructor
     * @param Webconferencing $webconferencing The webconferencing which
     * provides this component
     */
    protected function WebconferencingManagerComponent($webconferencing)
    {
        $this->pm = $webconferencing;
        $this->id = ++ self :: $component_count;
    }

    /**
     * @see WebconferencingManager :: redirect()
     */
    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
    }

    /**
     * @see WebconferencingManager :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see WebconferencingManager :: get_parameters()
     */
    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    /**
     * @see WebconferencingManager :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        return $this->get_parent()->set_parameter($name, $value);
    }

    /**
     * @see WebconferencingManager :: get_url()
     */
    function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
    }

    /**
     * @see WebconferencingManager :: display_header()
     */
    function display_header($breadcrumbtrail, $display_search = false)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
    }

    /**
     * @see WebconferencingManager :: display_message()
     */
    function display_message($message)
    {
        return $this->get_parent()->display_message($message);
    }

    /**
     * @see WebconferencingManager :: display_error_message()
     */
    function display_error_message($message)
    {
        return $this->get_parent()->display_error_message($message);
    }

    /**
     * @see WebconferencingManager :: display_warning_message()
     */
    function display_warning_message($message)
    {
        return $this->get_parent()->display_warning_message($message);
    }

    /**
     * @see WebconferencingManager :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }

    /**
     * @see WebconferencingManager :: display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see WebconferencingManager :: display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see WebconferencingManager :: display_popup_form
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * @see WebconferencingManager :: get_parent
     */
    function get_parent()
    {
        return $this->pm;
    }

    /**
     * @see WebconferencingManager :: get_web_code_path
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * @see WebconferencingManager :: get_user()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see WebconferencingManager :: get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    //Data Retrieval
    

    function count_webconferences($condition)
    {
        return $this->get_parent()->count_webconferences($condition);
    }

    function retrieve_webconferences($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_webconferences($condition, $offset, $count, $order_property);
    }

    function retrieve_webconference($id)
    {
        return $this->get_parent()->retrieve_webconference($id);
    }

    function count_webconference_options($condition)
    {
        return $this->get_parent()->count_webconference_options($condition);
    }

    function retrieve_webconference_options($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_webconference_options($condition, $offset, $count, $order_property);
    }

    function retrieve_webconference_option($id)
    {
        return $this->get_parent()->retrieve_webconference_option($id);
    }

    // Url Creation
    

    function get_create_webconference_url()
    {
        return $this->get_parent()->get_create_webconference_url();
    }

    function get_update_webconference_url($webconference)
    {
        return $this->get_parent()->get_update_webconference_url($webconference);
    }

    function get_delete_webconference_url($webconference)
    {
        return $this->get_parent()->get_delete_webconference_url($webconference);
    }

    function get_browse_webconferences_url()
    {
        return $this->get_parent()->get_browse_webconferences_url();
    }

    function get_create_webconference_option_url()
    {
        return $this->get_parent()->get_create_webconference_option_url();
    }

    function get_update_webconference_option_url($webconference_option)
    {
        return $this->get_parent()->get_update_webconference_option_url($webconference_option);
    }

    function get_delete_webconference_option_url($webconference_option)
    {
        return $this->get_parent()->get_delete_webconference_option_url($webconference_option);
    }

    function get_browse_webconference_options_url()
    {
        return $this->get_parent()->get_browse_webconference_options_url();
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    /**
     * Create a new profile component
     * @param string $type The type of the component to create.
     * @param Profile $webconferencing The pm in
     * which the created component will be used
     */
    static function factory($type, $webconferencing)
    {
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'WebconferencingManager' . $type . 'Component';
        require_once $filename;
        return new $class($webconferencing);
    }
}
?>