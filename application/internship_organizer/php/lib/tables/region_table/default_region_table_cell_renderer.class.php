<?php
/**
 * $Id: default_region_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package region.lib.region_table
 */
/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerRegionTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerRegionTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $region)
    {
        switch ($column->get_name())
        {
            case InternshipOrganizerRegion :: PROPERTY_ID :
                return $region->get_id();
            case InternshipOrganizerRegion :: PROPERTY_CITY_NAME :
                return $region->get_city_name();
            case InternshipOrganizerRegion :: PROPERTY_ZIP_CODE :
                return $region->get_zip_code();    
            case InternshipOrganizerRegion :: PROPERTY_DESCRIPTION :
                return $region->get_description();
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