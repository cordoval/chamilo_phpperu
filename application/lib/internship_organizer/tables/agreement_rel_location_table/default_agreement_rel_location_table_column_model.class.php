<?php
/**
 * $Id: default_agreement_rel_location_table_column_model.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package agreement.lib.agreement_rel_location_table
 */

/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerAgreementRelLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerAgreementRelLocationTableColumnModel()
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
        
        $columns = array();
        $columns[] = new ObjectTableColumn(InternshipOrganizerAgreementRelLocation :: PROPERTY_PREFERENCE_ORDER, true);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_NAME, false);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_ADDRESS, false);
        $region_column = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_REGION_ID, false);
        $region_column->set_title(Translation :: get('City'));
        $columns[] = $region_column;
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_DESCRIPTION, false);
        
        // $columns[] = new ObjectTableColumn(InternshipOrganizerLocation:: PROPERTY_CITY, true, $location_alias);
        //        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation:: PROPERTY_STREET, true, $location_alias);
        

        return $columns;
    }
}
?>