<?php
/**
 * $Id: content_object_difference.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * A class to display a ContentObject.
 */
abstract class ContentObjectDifference
{
    /**
     * The learning object.
     */
    private $object;
    /**
     * The learning object version.
     */
    private $version;

    /**
     * Constructor.
     * @param ContentObject $object The learning object to compare.
     * @param ContentObject $version The learning object to compare with.
     */
    protected function ContentObjectDifference($version, $object)
    {
        $this->object = $object;
        $this->version = $version;
    }

    /**
     * Returns the learning object associated with this object.
     * @return ContentObject The object.
     */
    function get_object()
    {
        return $this->object;
    }

    /**
     * Returns the learning object associated with this object.
     * @return ContentObject The object version.
     */
    function get_version()
    {
        return $this->version;
    }

    function get_difference()
    {
        $object_string = $this->object->get_description();
        $object_string = str_replace('<p>', '', $object_string);
        $object_string = str_replace('</p>', "<br />\n", $object_string);
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = $this->version->get_description();
        $version_string = str_replace('<p>', '', $version_string);
        $version_string = str_replace('</p>', "<br />\n", $version_string);
        $version_string = explode("\n", strip_tags($version_string));
        
        $td = new Difference_Engine($version_string, $object_string);
        //$td = new Difference_Engine($object_string, $version_string);
        

        return $td->getDiff();
    }

    /**
     * Creates an object that can display the given learning object in a
     * standardized fashion.
     * @param ContentObject $object The object to display.
     * @return ContentObject
     */
    static function factory(&$object, &$version)
    {
        $type = $object->get_type();
        $class = ContentObject :: type_to_class($type) . 'Difference';
        require_once dirname(__FILE__) . '/content_object/' . $type . '/' . $type . '_difference.class.php';
        return new $class($object, $version);
    }
}
?>