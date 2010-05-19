<?php
require_once Path :: get_admin_path() . 'lib/package_manager/package_dependency_verifier.class.php';

abstract class PackageUpdaterType
{
    private $parent;
    private $source;

    function PackageUpdaterType($parent, $source)
    {
        $this->set_parent($parent);
        $this->source = $source;
    }

    function get_source()
    {
        return $this->source;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function set_parent($parent)
    {
        $this->parent = $parent;
    }

    function add_message($message, $type = PackageUpdater :: TYPE_NORMAL)
    {
        $this->get_parent()->add_message($message, $type);
    }

    function update_failed($error_message)
    {
        $this->get_parent()->update_failed($error_message);
    }

    function update_successful($type)
    {
        $this->get_parent()->update_successful($type);
    }

    function process_result($type)
    {
        $this->get_parent()->process_result($type);
    }

    abstract function update();

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of package installer type.
     */
    static function factory($parent, $type, $source)
    {
        $class = 'PackageUpdater' . Utilities :: underscores_to_camelcase($type) . 'Type';
        require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
        return new $class($parent, $source);
    }
}
?>