<?php
/**
 * $Id: gallery_object_table_property_model.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.table.object_table
 */

class GalleryObjectTablePropertyModel
{
    /**
     * The properties in the table.
     */
    private $properties;
    /**
     * The property by which the table is currently sorted.
     */
    private $order_property;
    /**
     * The direction in which the table is currently sorted.
     */
    private $order_direction;

    /**
     * Constructor. Creates a new object table model.
     * @param array $properties The properties to use in the table. An array of
     * TableProperty instances.
     * @param int $default_order_property The property to order objects by, by
     * default, passed as the index of the
     * property in $properties.
     * @param string $default_order_direction The default order direction.
     * Either the PHP constant SORT_ASC
     * or SORT_DESC.
     */
    function GalleryObjectTablePropertyModel($properties, $default_order_property = 0, $default_order_direction = SORT_ASC)
    {
        $this->properties = $properties;
        $this->order_property = $default_order_property;
        $this->order_direction = $default_order_direction;
    }

    /**
     * Gets the number of properties in the model.
     * @return int The property count.
     */
    function get_property_count()
    {
        return count($this->properties);
    }

    /**
     * Gets the property at the given index in the model.
     * @param int $index The index.
     * @return ContentObjectTableProperty The property.
     */
    function get_property($index)
    {
        return $this->properties[$index];
    }

    function get_properties()
    {
        return $this->properties;
    }

    function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Adds the given property at the end of the table.
     * @param ContentObjectTableProperty The property.
     */
    function add_property($property)
    {
        $this->properties[] = $property;
    }

    /**
     * Gets the index of the property to order objects by, by default.
     * @return int The property index.
     */
    function get_default_order_property()
    {
        return $this->order_property;
    }

    /**
     * Sets the index of the property to order objects by, by default.
     * @param int $property The index.
     */
    function set_default_order_property($property_index)
    {
        $this->order_property = $property_index;
    }

    /**
     * Gets the default order direction.
     * @return string The direction. Either the PHP constant SORT_ASC or
     * SORT_DESC.
     */
    function get_default_order_direction()
    {
        return $this->order_direction;
    }

    /**
     * Sets the default order direction.
     * @param string $direction The direction. Either the PHP constant SORT_ASC
     * or SORT_DESC.
     */
    function set_default_order_direction($direction)
    {
        $this->order_direction = $direction;
    }

    function get_order_property($property_number, $order_direction)
    {
        $property = $this->get_property($property_number);

        // If it's an ObjectTableProperty, then return the property
        if ($property instanceof GalleryObjectTableProperty)
        {
            return new ObjectTableOrder($property->get_property(), $order_direction, $property->get_storage_unit_alias());
        }
        // If not, return the default order property
        else
        {
            $default_property = $this->get_property($this->get_default_order_property());

            // Make sure the default order property is actually an ObjectTableProperty AND sortabele
            if ($default_property instanceof GalleryObjectTableProperty)
            {
                return new ObjectTableOrder($default_property->get_property(), $order_direction, $default_property->get_storage_unit_alias());
            }
            // If not, just don't sort (probably a table with display orders)
            else
            {
                return null;
            }
        }
    }
}
?>