<?php
/**
 * $Id: default_category_rel_period_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_period_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryRelPeriodTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryRelPeriodTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $dm = InternshipOrganizerDataManager :: get_instance();
        $category_alias = $dm->get_alias(InternshipOrganizerCategory :: get_table_name());
		
		$columns = array ();
		$columns [] = new ObjectTableColumn ( InternshipOrganizerCategory::PROPERTY_NAME, true, $category_alias );
		$columns [] = new ObjectTableColumn ( InternshipOrganizerCategory::PROPERTY_DESCRIPTION, true, $category_alias );
        
        return $columns;
    }
}
?>