<?php
/**
 * $Id: default_category_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryTableColumnModel()
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
        $columns[] = new ObjectTableColumn(InternshipOrganizerCategory :: PROPERTY_NAME);
        $columns[] = new ObjectTableColumn(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION);
        return $columns;
    }
}
?>