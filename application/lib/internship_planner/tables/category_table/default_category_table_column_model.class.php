<?php
/**
 * $Id: default_category_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipPlannerCategoryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipPlannerCategoryTableColumnModel()
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
        $columns[] = new ObjectTableColumn(InternshipPlannerCategory :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(InternshipPlannerCategory :: PROPERTY_CODE);
        $columns[] = new ObjectTableColumn(InternshipPlannerCategory :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>