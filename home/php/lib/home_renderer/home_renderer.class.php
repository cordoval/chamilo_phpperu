<?php
/**
 * @author Hans De Bisschop
 */
abstract class HomeRenderer
{
    const TYPE_DEFAULT = 'default';
    const PARAM_TAB_ID = 'tab';

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param User|null $user
     */
    function HomeRenderer($user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    function get_user()
    {
        return $this->user;
    }

    function get_user_id()
    {
        return $this->get_user()->get_id();
    }

    /**
     * @param string $type
     * @param User|null $user
     * @return MenuRenderer
     */
    static function factory($type, $user)
    {

        $file = dirname(__FILE__) . '/renderer/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('HomeRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'HomeRenderer';
        $renderer = new $class($user);
        return $renderer;
    }

    /**
     * @param string $type
     * @param User|null $user
     * @return string
     */
    static function as_html($type, $user)
    {
        return self :: factory($type, $user)->render();
    }

    /**
     * @return string
     */
    abstract function render();

    public function display_header()
    {
        Display :: header();
    }

    public function display_footer()
    {
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    public function get_current_tab()
    {
        return Request :: get(self :: PARAM_TAB_ID);
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        //        $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) : $this->get_parameters());
        return Redirect :: get_url($parameters, $filter, $encode_entities);
    }

    /**
     * Gets a link to the personal calendar application
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_INDEX)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        return Redirect :: get_link(PersonalCalendarManager :: APPLICATION_NAME, $parameters, $filter, $encode_entities, $application_type);
    }

    function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
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
}
?>