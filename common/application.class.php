<?php
/**
 * $Id: application.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */

abstract class Application
{
    private $user;
    
    private $parameters;
    private $search_parameters;
    
    private $breadcrumbs;
    
    const PARAM_ACTION = 'go';
    
    const PARAM_MESSAGE = 'message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_APPLICATION = 'application';
    
    const PLACEHOLDER_APPLICATION = '__APPLICATION__';

    function Application($user)
    {
        $this->user = $user;
        $this->parameters = array();
        $this->search_parameters = array();
        $this->breadcrumbs = array();
        
    //        $action = Request :: get(self :: PARAM_ACTION);
    //        if ($action)
    //        {
    //            $this->set_action(Request :: get(self :: PARAM_ACTION));
    //        }
    //
    //        if (Request :: get(self :: PARAM_APPLICATION) == $this->get_application_name())
    //        {
        //        $this->handle_table_action();
    //        }
    }

    function handle_table_action()
    {
        $table_name = Request :: post('table_name');
        if (isset($table_name))
        {
            $class = Utilities :: underscores_to_camelcase($table_name);
            if (class_exists($class))
            {
                call_user_func(array($class, 'handle_table_action'));
                
                $table_action_name = Request :: post($table_name . '_action_name');
                $table_action_value = Request :: post($table_name . '_action_value');
                $this->set_parameter($table_action_name, $table_action_value);
                Request :: set_get($table_action_name, $table_action_value);
            }
        }
    }

    /**
     * Gets the URL of the current page in the application. Optionally takes
     * an associative array of name/value pairs representing additional query
     * string parameters; these will either be added to the parameters already
     * present, or override them if a value with the same name exists.
     * @param array $parameters The additional parameters, or null if none.
     * @param boolean $encode Whether or not to encode HTML entities. Defaults
     * to false.
     * @return string The URL.
     */
    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());
        return Redirect :: get_url($parameters, $filter, $encode_entities);
    }

    //abstract static function get_application_class_name($application);
    

    /**
     * Creates a new instance of the given application
     * @param string $application
     * @return Application An instance of the application corresponding to the
     * given $application
     */
    static function factory($application, $user = null)
    {
        if (BasicApplication :: is_application($application))
        {
            return BasicApplication :: factory($application, $user);
        }
        else
        {
            return LauncherApplication :: factory($application, $user);
        }
    }

    /**
     * Redirect the end user to another location.
     * The current url will be used as the basis.
     * @param array $parameters Parameters to be added to the url
     * @param array $filter Parameters to be filtered from the url
     * @param boolean $encode
     * @param string $type Redirect to an url or a link
     */
    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_APPLICATION)
    {
        switch ($redirect_type)
        {
            case Redirect :: TYPE_URL :
                $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());
                Redirect :: url($parameters, $filter, $encode_entities);
                break;
            case Redirect :: TYPE_LINK :
                // Use this untill PHP 5.3 is available
                // Then use get_class($this) :: APPLICATION_NAME
                // and remove the get_application_name function();
                $application = $this->get_application_name();
                Redirect :: link($application, $parameters, $filter, $encode_entities, $application_type);
                break;
        }
        exit();
    }

    /**
     * Redirect the end user to another location.
     * The current url will be used as the basis.
     * This method allows passing on messages directly instead of using the parameters array
     * @param string $message The message to show (default = no message).
     * @param boolean $error_message Is the passed message an error message?
     * @param array $parameters Parameters to be added to the url
     * @param array $filter Parameters to be filtered from the url
     * @param boolean $encode
     * @param string $type Redirect to an url or a link
     */
    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_APPLICATION)
    {
        if (! $error_message)
        {
            $parameters[self :: PARAM_MESSAGE] = $message;
        }
        else
        {
            $parameters[self :: PARAM_ERROR_MESSAGE] = $message;
        }
        
        $this->simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    /**
     * Returns the current URL parameters.
     * @return array The parameters.
     */
    function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the value of the given URL parameter.
     * @param string $name The parameter name.
     * @return string The parameter value.
     */
    function get_parameter($name)
    {
        if (array_key_exists($name, $this->parameters))
            return $this->parameters[$name];
    }

    /**
     * Sets the value of a URL parameter.
     * @param string $name The parameter name.
     * @param string $value The parameter value.
     */
    function set_parameter($name, $value)
    {
        //dump(get_class($this) . ' | ' . $name);
        $this->parameters[$name] = $value;
    }

    /**
     * @param array $parameters
     */
    function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    function set_breadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    function get_breadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Displays the portal header. This does NOT include:
     * - breadcrumbs
     * - title
     * - menu
     * - ...
     */
    function display_portal_header()
    {
        Display :: header();
    }

    /**
     * Displays the portal footer.
     * To be used in conjunction with
     * Application :: display_portal_header()
     */
    function display_portal_footer()
    {
        Display :: footer();
    }

    /**
     * Displays the header.
     * @param BreadcrumbTrail $breadcrumbtrail The breadcrumbtrail to show in the header.
     */
    function display_header($breadcrumbtrail = null, $display_title = true)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();
            if ($breadcrumbtrail->size() == 1)
            {
                $breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get(Utilities :: underscores_to_camelcase($this->get_application_name()))));
            }
        }
        
        $categories = $this->get_breadcrumbs();
        if (count($categories) > 0)
        {
            foreach ($categories as $category)
            {
                $breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
            }
        }
        
        $title = $breadcrumbtrail->get_last()->get_name();
        Display :: header($breadcrumbtrail);
        
        // If there is an application-wide menu, show it
        if ($this->has_menu())
        {
            echo '<div style="float: left; width: 15%;">';
            echo $this->get_menu();
            echo '</div>';
            echo '<div style="float: right; width: 85%;">';
        }
        
        if ($display_title)
            echo '<h3 style="float: left;" title="' . $title . '">' . Utilities :: truncate_string($title) . '</h3>';
        echo '<div class="clear">&nbsp;</div>';
        
        $message = Request :: get(self :: PARAM_MESSAGE);
        if ($message)
        {
            $this->display_message($message);
        }
        
        $message = Request :: get(self :: PARAM_ERROR_MESSAGE);
        if ($message)
        {
            $this->display_error_message($message);
        }
        
        $message = Request :: get(self :: PARAM_WARNING_MESSAGE);
        if ($message)
        {
            $this->display_warning_message($message);
        }
    }

    function display_footer()
    {
        // In wase there was an application-wide menu, properly end it
        if ($this->has_menu())
        {
            echo '<div class="clear">&nbsp;</div>';
            echo '</div>';
        }
        
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    /**
     * Displays a normal message.
     * @param string $message The message.
     */
    function display_message($message)
    {
        Display :: normal_message($message);
    }

    /**
     * Displays an error message.
     * @param string $message The message.
     */
    function display_error_message($message)
    {
        Display :: error_message($message);
    }

    /**
     * Displays a warning message.
     * @param string $message The message.
     */
    function display_warning_message($message)
    {
        Display :: warning_message($message);
    }

    /**
     * Displays an error page.
     * @param string $message The message.
     */
    function display_error_page($message)
    {
        $this->display_header();
        $this->display_error_message($message);
        $this->display_footer();
    }

    /**
     * Displays a warning page.
     * @param string $message The message.
     */
    function display_warning_page($message)
    {
        $this->display_header();
        $this->display_warning_message($message);
        $this->display_footer();
    }

    /**
     * Displays a popup form.
     * @param string $message The message.
     */
    function display_popup_form($form_html)
    {
        Display :: normal_message($form_html);
    }

    /**
     * Wrapper for Display :: not_allowed();.
     */
    function not_allowed($trail = null, $show_login_form = true)
    {
        Display :: not_allowed($trail, $show_login_form);
    }

    /**
     * Gets the user id of this personal calendars owner
     * @return int The id of the current user
     */
    function get_user_id()
    {
        return $this->user->get_id();
    }

    /**
     * Gets the user.
     * @return User The User object of the current user
     */
    function get_user()
    {
        return $this->user;
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_ACTION);
    }

    /**
     * Sets the current action.
     * @param string $action The new action.
     */
    function set_action($action)
    {
        return $this->set_parameter(self :: PARAM_ACTION, $action);
    }

    function get_platform_setting($variable)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        $application = $this->get_application_name();
        return PlatformSetting :: get($variable, $application);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    abstract function get_application_name();

    /**
     * Returns a list of actions available to the admin.
     * @return Array $info Contains all possible actions.
     */
    public static function get_application_platform_admin_links($application = self :: PARAM_APPLICATION)
    {
        $info = array();
        $info['application'] = array('name' => Translation :: get(self :: application_to_class($application)), 'class' => $application);
        $info['links'] = array();
        $info['search'] = null;
        
        return $info;
    }

    /**
     * Returns a list of actions available to the admin.
     * @return Array $info Contains all possible actions.
     */
    public function get_application_platform_import_links()
    {
        return array();
    }

    /**
     * Does the entire application have a leftside menu? False per default.
     * Can be overwritten by the specific application
     * @return boolean $has_menu True or false
     */
    public function has_menu()
    {
        return false;
    }

    /**
     * Returns the html for the application-menu. Empty per default
     * Can be overwritten by the specific application
     * @return String $menu The menu html
     */
    public function get_menu()
    {
        return '';
    }

    /**
     * Converts an application name to the corresponding class name.
     * @param string $application The application name.
     * @return string The class name.
     */
    public static function application_to_class($application)
    {
        return Utilities :: underscores_to_camelcase($application);
    }

    /**
     * Converts a class name to the corresponding application name.
     * @param string $application The class name.
     * @return string The application name.
     */
    public static function class_to_application($application)
    {
        return Utilities :: _camelcase_to_underscores($application);
    }

    /**
     * Determines if a given name is the name of an application
     * @param string $name
     * @return boolean
     * @todo Better would be to check if the class for the application exists
     */
    public static function is_application_name($name)
    {
        return (preg_match('/^[a-z][a-z_]+$/', $name) > 0);
    }

    /**
     * Create a new application component
     * @param string $type The type of the component to create.
     * @param Application $manager The application in
     * which the created component will be used
     */
    function create_component($type, $application = null)
    {
        if ($application == null)
        {
            $application = $this;
        }
        
        $manager_class = get_class($application);
        $application_component_path = $application->get_application_component_path();
        
        $file = $application_component_path . Utilities :: camelcase_to_underscores($type) . '.class.php';
        
        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($manager_class) . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';
            
            $application_name = Application :: application_to_class($this->get_application_name());
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb('#', Translation :: get($application_name)));
            
            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        
        $class = $manager_class . $type . 'Component';
        require_once $file;
        
        if (is_subclass_of($application, 'SubManager'))
        {
            $component = new $class($application->get_parent());
        }
        else
        {
            $component = new $class($this->get_user());
            $component->set_parameters($this->get_parameters());
        }
        return $component;
    }

    /**
     * Runs the application.
     */
    //abstract function run();
    

    abstract static function get_application_path($application_name);

    //abstract static function get_application_manager_path($application_name);
    

    function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, $succes_message_multiple)
    {
        if ($failures)
        {
            if ($count == 1)
            {
                $message = $fail_message_single;
            }
            else
            {
                $message = $fail_message_multiple;
            }
        }
        else
        {
            if ($count == 1)
            {
                $message = $succes_message_single;
            }
            else
            {
                $message = $succes_message_multiple;
            }
        }
        
        return Translation :: get($message);
    }

    /**
     * EXPERIMENTAL ENHANCEMENTS
     * @author Hans De Bisschop
     */
    
    /**
     * @param string $application
     */
    static function get_application_class_name($application)
    {
        if (BasicApplication :: is_application($application))
        {
            return BasicApplication :: get_application_class_name($application);
        }
        else
        {
            return LauncherApplication :: get_application_class_name($application);
        }
    }

    /**
     * @param string $application
     * @return string|NULL
     */
    static function get_component_path($application)
    {
        if (BasicApplication :: is_application($application))
        {
            return BasicApplication :: get_component_path($application);
        }
        else
        {
            return null;
        }
    }

    /**
     * @param string $application
     * @return string
     */
    static function get_application_manager_path($application)
    {
        if (BasicApplication :: is_application($application))
        {
            return BasicApplication :: get_application_manager_path($application);
        }
        else
        {
            return LauncherApplication :: get_application_manager_path($application);
        }
    }

    /**
     * @return string|NULL
     */
    function process_table_action()
    {
        $table_name = Request :: post('table_name');
        if (isset($table_name))
        {
            $class = Utilities :: underscores_to_camelcase($table_name);
            if (class_exists($class))
            {
                call_user_func(array($class, 'handle_table_action'));
                
                $table_action_name = Request :: post($table_name . '_action_name');
                $table_action_value = Request :: post($table_name . '_action_value');
                //                $this->set_parameter($table_action_name, $table_action_value);
                Request :: set_get($table_action_name, $table_action_value);
                
                if ($table_action_name == self :: PARAM_ACTION)
                {
                    return $table_action_value;
                }
            }
        }
        
        return null;
    }

    /**
     * @param string $application_name
     * @param string $type
     * @return string
     */
    private static function load_class($application_name, $type)
    {
        $manager_class = self :: get_application_class_name($application_name);
        $application_component_path = self :: get_component_path($application_name);
        
        $file = $application_component_path . Utilities :: camelcase_to_underscores($type) . '.class.php';
        
        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('ComponentFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($manager_class) . '</li>';
            $message[] = '<li>' . Translation :: get($type) . '</li>';
            $message[] = '</ul>';
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb('#', Translation :: get($manager_class)));
            
            Display :: header($trail);
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        
        require_once $file;
        return $manager_class . Utilities :: underscores_to_camelcase($type) . 'Component';
    }

    /**
     * @param string $application_name
     * @param User $user
     * @param string $type
     * @return Application
     */
    static function component($application_name, $user, $type)
    {
        $class = self :: load_class($application_name, $type);
        return new $class($user);
    }

    /**
     * @param string $application_name
     * @return string
     */
    static function get_component_action($application_name)
    {
        $manager_class = self :: get_application_class_name($application_name);
        $default_action = call_user_func(array($manager_class, 'get_default_action'));
        
        $action = Request :: get(self :: PARAM_ACTION);
        $action = ! isset($action) ? $default_action : $action;
        
        $table_action = self :: process_table_action();
        if ($table_action)
        {
            $action = $table_action;
        }
        
        return $action;
    }

    /**
     * @param string $application_name
     * @param User $user
     */
    static function construct($application_name, $user)
    {
        require_once self :: get_application_manager_path($application_name);
        $action = self :: get_component_action($application_name);
        
        $component = self :: component($application_name, $user, $action);
        
        $component->set_parameter(self :: PARAM_APPLICATION, $application_name);
        
        $trail = BreadcrumbTrail :: get_instance();
        
        if($component instanceof AdministrationComponent)
        {
        	$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        	$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => $application_name), array(), false, Redirect :: TYPE_CORE), Translation :: get(self :: application_to_class($application_name))));
        }
        else
        {
        	$trail->add(new Breadcrumb($component->get_url(), Translation :: get(self :: application_to_class($application_name))));
                //$trail->add(new Breadcrumb(Redirect :: get_link($application_name)));
        }
        
        $component->add_additional_breadcrumbs($trail);
        
        $parameters = $component->get_additional_parameters();
        foreach($parameters as $parameter)
        {
       		$component->set_parameter($parameter, Request :: get($parameter));
        }
        
        $component->set_action($action);
        
        if(!$component instanceof DelegateComponent)
        {
        	$trail->add(new Breadcrumb($component->get_url(array(self :: PARAM_ACTION => $action)), Translation :: get(get_class($component))));
        }
        
        return $component;
    }

    /**
     * @param string $application_name
     * @param User $user
     */
    static function launch($application_name, $user)
    {
        self :: construct($application_name, $user)->run();
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	
    }

	function get_additional_parameters()
    {
    	return array();
    }
}
?>