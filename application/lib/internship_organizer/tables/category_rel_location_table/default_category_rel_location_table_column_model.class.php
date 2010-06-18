<?php
/**
 * $Id: default_category_rel_location_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package category.lib.category_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerCategoryRelLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerCategoryRelLocationTableColumnModel()
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
        $organisation_alias = $dm->get_alias(InternshipOrganizerOrganisation :: get_table_name());
        $location_alias = $dm->get_alias(InternshipOrganizerLocation :: get_table_name());
        $region_alias = $dm->get_alias(InternshipOrganizerRegion :: get_table_name());
        
    	$columns = array();
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_NAME, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_ADDRESS, true, $location_alias);
        
        $region_column = new ObjectTableColumn ( InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, true, $region_alias );
		$region_column->set_title(Translation :: get('ZipCode'));
		$columns [] = $region_column;
		
		$region_column = new ObjectTableColumn ( InternshipOrganizerRegion :: PROPERTY_CITY_NAME, true, $region_alias );
		$region_column->set_title(Translation :: get('City'));
		$columns [] = $region_column;
		
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, true, $location_alias);
        
       // $columns[] = new ObjectTableColumn(InternshipOrganizerLocation:: PROPERTY_CITY, true, $location_alias);
//        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation:: PROPERTY_STREET, true, $location_alias);
        
        
        return $columns;
    }
}
?>