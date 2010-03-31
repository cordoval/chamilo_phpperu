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
        parent :: __construct(self :: get_default_columns());
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $dm = InternshipPlannerDataManager :: get_instance();
        $organisation_alias = $dm->get_database()->get_alias(InternshipPlannerOrganisation :: get_table_name());
        $location_alias = $dm->get_database()->get_alias(InternshipPlannerLocation :: get_table_name());
        
        
    	$columns = array();
        $columns[] = new ObjectTableColumn(InternshipPlannerOrganisation :: PROPERTY_NAME, true, $organisation_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerOrganisation :: PROPERTY_DESCRIPTION, true, $organisation_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation :: PROPERTY_NAME, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation:: PROPERTY_CITY, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipPlannerLocation:: PROPERTY_STREET, true, $location_alias);
        
        
        return $columns;
    }
}
?>