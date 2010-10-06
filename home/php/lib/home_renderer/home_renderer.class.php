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

    function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
    }
}
?>