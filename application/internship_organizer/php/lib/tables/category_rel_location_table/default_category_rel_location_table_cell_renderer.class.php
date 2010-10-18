<?php
/**
 * $Id: default_category_rel_location_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryRelLocationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $category_rel_location)
    {
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_ADDRESS);
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
                return $category_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                return $category_rel_location->get_optional_property(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
            case InternshipOrganizerLocation :: PROPERTY_TELEPHONE :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_TELEPHONE);
            case InternshipOrganizerLocation :: PROPERTY_FAX :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_FAX);
            case InternshipOrganizerLocation :: PROPERTY_EMAIL :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_EMAIL);
            case InternshipOrganizerLocation :: PROPERTY_DESCRIPTION :
                return $category_rel_location->get_optional_property(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION);
            
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