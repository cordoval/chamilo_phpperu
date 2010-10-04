<?php
/**
 * $Id: gallery_object_table_property.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */

class GalleryObjectTableProperty
{
    /**
     * The property of the object which will be displayed in this
     * column.
     */
    private $property;
    /**
     * The title of the column.
     */
    private $title;

    private $storage_unit_alias;

    /**
     * Constructor. Either defines a column that displays a default property
     * of learning objects, or arbitrary content.
     * @param string $property If the column contains
     *                                              arbitrary content, the
     *                                              title of the column. If
     *                                              it displays a learning
     *                                              object property, that
     *                                              particular property, a
     *                                              ContentObject::PROPERTY_*
     *                                              constant.
     * @param boolean $contains_content_object_property True if the column
     *                                                   displays a learning
     *                                                   object property, false
     *                                                   otherwise.
     */
    function GalleryObjectTableProperty($property, $storage_unit_alias = null)
    {
        $this->property = $property;
        $this->title = Translation :: get(Utilities :: underscores_to_camelcase($this->property));
        $this->storage_unit_alias = $storage_unit_alias;
    }

    /**
     * Gets the learning object property that this column displays.
     * @return string The property name, or null if the column contains
     *                arbitrary content.
     */
    function get_property()
    {
        return $this->property;
    }

    /**
     * Gets the title of this column.
     * @return string The title.
     */
    function get_title()
    {
        return $this->title;
    }

    function get_storage_unit_alias()
    {
        return $this->storage_unit_alias;
    }

    /**
     * Sets the title of this column.
     * @param string $title The new title.
     */
    function set_title($title)
    {
        $this->title = $title;
    }

    function get_name()
    {
        return $this->get_property();
    }
}
?>