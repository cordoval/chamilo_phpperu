<?php

class DefaultInternshipOrganizerMentorRelLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMentorRelLocationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    private static function get_default_columns()
    {
        
        $dm = InternshipOrganizerDataManager :: get_instance();
        $region_alias = $dm->get_alias(InternshipOrganizerRegion :: get_table_name());
        $location_alias = $dm->get_alias(InternshipOrganizerLocation :: get_table_name());
        
        $columns = array();
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_NAME, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_ADDRESS, true, $location_alias);
        
        $region_column = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, true, $region_alias);
        $region_column->set_title(Translation :: get('ZipCode'));
        $columns[] = $region_column;
        
        $region_column = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, true, $region_alias);
        $region_column->set_title(Translation :: get('City'));
        $columns[] = $region_column;
        
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, true, $location_alias);
        
        return $columns;
    
    }
}
?>