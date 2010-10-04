<?php
/**
 * $Id: default_content_object_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object_table
 */

/**
 * This is the default column model, used when a ContentObjectTable does not
 * provide its own model.
 *
 * The default model contains the following columns:
 *
 * - The type of the learning object
 * - The title of the learning object
 * - The description of the learning object
 * - The date when the learning object was last modified
 *
 * Although this model works best in conjunction with the default cell
 * renderer, it can be used with any ContentObjectTableCellRenderer.
 *
 * @see ContentObjectTable
 * @see ContentObjectTableColumnModel
 * @see DefaultContentObjectTableCellRenderer
 * @author Tim De Pauw
 */
class DefaultContentObjectGalleryTablePropertyModel extends GalleryObjectTablePropertyModel
{

    /**
     * Constructor
     */
    function DefaultContentObjectGalleryTablePropertyModel()
    {
        parent :: __construct(self :: get_default_properties(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_properties()
    {
        $properties = array();
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_TITLE);
        $properties[] = new GalleryObjectTableProperty(ContentObject :: PROPERTY_DESCRIPTION);
        return $properties;
    }
}
?>