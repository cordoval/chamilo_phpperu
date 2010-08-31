<?php
/**
 * @author Hans De Bisschop
 */
abstract class MenuRenderer
{
    const TYPE_MINI_BAR = 'mini_bar';
    const TYPE_SITE_MAP = 'site_map';
    const TYPE_TREE = 'tree';
    const TYPE_BAR = 'bar';

    /**
     * @var User|null
     */
    private $user;

    /**
     * @param User|null $user
     */
    function MenuRenderer($user = null)
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
            throw new Exception(Translation :: get('MenuRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'MenuRenderer';
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
}
?>