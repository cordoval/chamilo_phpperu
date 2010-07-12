<?php
abstract class ExternalRepositoryObjectDisplay
{
    private $object;

    function ExternalRepositoryObjectDisplay($object)
    {
        $this->object = $object;
    }

    static function factory($object)
    {
        $type = $object->get_type();
        $class = Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryObjectDisplay';
        require_once dirname(__FILE__) . '/type/' . $type . '/' . $type . '_external_repository_object_display.class.php';
        return new $class($object);
    }

    function get_object()
    {
        return $this->object;
    }

    abstract function as_html();
}
?>