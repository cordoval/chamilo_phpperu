<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipOrganizerMomentRelLocationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMomentRelLocationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        
        $moment_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerMoment :: get_table_name());
        $agreement_rel_user_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreementRelUser :: get_table_name());
        $user_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        $location_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerLocation :: get_table_name());
        $region_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerRegion :: get_table_name());
        $agreement_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerAgreement :: get_table_name());
        $period_alias = InternshipOrganizerDataManager :: get_instance()->get_alias(InternshipOrganizerPeriod :: get_table_name());
        
        
        $columns = array();
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_NAME, true, $moment_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, true, $moment_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_BEGIN, true, $moment_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_END, true, $moment_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerAgreement :: PROPERTY_NAME, true, $agreement_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerAgreement :: PROPERTY_DESCRIPTION, true, $agreement_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerPeriod :: PROPERTY_NAME, true, $period_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_NAME, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_ADDRESS, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerLocation :: PROPERTY_TELEPHONE, true, $location_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, true, $region_alias);
        $columns[] = new ObjectTableColumn(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, true, $region_alias);
        
        return $columns;
    }
}
?>