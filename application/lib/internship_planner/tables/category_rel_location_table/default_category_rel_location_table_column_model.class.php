<?php
/**
 * $Id: default_category_rel_location_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipPlannerCategoryRelLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerCategoryRelLocationTableColumnModel()
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
        $columns[] = new ObjectTableColumn(InternshipPlannerCategoryRelLocation :: PROPERTY_LOCATION_ID, false);
        return $columns;
    }
}
?>