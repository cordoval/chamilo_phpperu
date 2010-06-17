<?php
/**
 * $Id: default_content_object_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object_table
 */

/**
 * This is the default cell renderer, used when a ContentObjectTable does not
 * provide its own renderer.
 *
 * The default renderer provides a custom rendering method for the following
 * columns:
 *
 * - The ID of the learning object
 *   Displays the ID.
 * - The type of the learning object
 *   Displays the icon that corresponds to the learning object type.
 * - The title of the learning object
 *   Displays the title.
 * - The description of the learning object
 *   Strips HTML tags from the description of the learning object and displays
 *   the first 200 characters of the resulting string.
 * - The date when the learning object was created
 *   Displays a localized version of the date.
 * - The date when the learning object was last modified
 *   Displays a localized version of the date.
 *
 * Any other column type will result in an empty cell.
 *
 * @see ContentObjectTable
 * @see ContentObjectTableCellRenderer
 * @see DefaultContentObjectTableColumnModel
 * @author Tim De Pauw
 */
class DefaultContentObjectTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultContentObjectTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_ID :
                return $content_object->get_id();
            case ContentObject :: PROPERTY_TYPE :
                $type = $content_object->get_type();
                $icon = $content_object->get_icon_name();
                return '<img src="' . Theme :: get_common_image_path() . 'content_object/' . $icon . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($type) . 'TypeName')) . '"/>';
            case ContentObject :: PROPERTY_TITLE :
                return Utilities :: truncate_string($content_object->get_title(), 50);
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($content_object->get_description(), 50);
            case ContentObject :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $content_object->get_creation_date());
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $content_object->get_modification_date());
            case Translation :: get('Versions') :
                return $content_object->get_version_count();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>