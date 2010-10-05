<?php
/**
 * $Id: default_region_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package region.lib.region_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerRegionTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerRegionTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $citycolumn = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_CITY_NAME);
        $citycolumn->set_title(Translation :: get('City'));
        $columns[] = $citycolumn;
        $columns[] = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE);
        $columns[] = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>