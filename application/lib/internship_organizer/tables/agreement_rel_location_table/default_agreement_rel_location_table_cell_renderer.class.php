<?php
/**
 * $Id: default_agreement_rel_location_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package agreement.lib.agreement_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerAgreementRelLocationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerAgreementRelLocationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $agreementrellocation)
    {
        
        //$columns[] = new ObjectTableColumn(InternshipOrganizerOrganisation :: PROPERTY_NAME, true, $organisation_alias);
       // $columns[] = new ObjectTableColumn(InternshipOrganizerOrganisation :: PROPERTY_DESCRIPTION, true, $organisation_alias);
        //$columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_NAME, true, $location_alias);
        //$columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_CITY, true, $location_alias);
       // $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_STREET, true, $location_alias);
        
        $location_id = $agreementrellocation->get_location_id();
        $location = InternshipOrganizerDataManager :: get_instance()->retrieve_location($location_id);
        $organisation = $location->get_organisation();
        $region = $location->get_region();
        
        switch ($column->get_name())
        {
            case InternshipOrganizerLocation :: PROPERTY_NAME :
                return $location->get_name();
            case InternshipOrganizerLocation :: PROPERTY_ADDRESS :
                return $location->get_address();    
            case InternshipOrganizerLocation :: PROPERTY_DESCRIPTION :
                return $location->get_description();
            case InternshipOrganizerLocation :: PROPERTY_REGION_ID :
            	$city_string = $region->get_zip_code() . '  ' . $region->get_city_name();
                return $city_string;
            //case InternshipOrganizerLocation :: PROPERTY_CITY :
              //  return $location->get_city();
           // case InternshipOrganizerLocation :: PROPERTY_STREET :
           //     return $location->get_street();
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