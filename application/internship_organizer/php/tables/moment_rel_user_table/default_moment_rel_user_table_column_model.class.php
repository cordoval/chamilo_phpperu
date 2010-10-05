<?php

require_once dirname(__FILE__) . '/../../moment.class.php';

class DefaultInternshipOrganizerMomentRelUserTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerMomentRelUserTableColumnModel()
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
        
        $columns = array();
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias, true);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias, true);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_NAME, true, $moment_alias, true);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_DESCRIPTION, true, $moment_alias, true);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_BEGIN, true, $moment_alias, true);
        $columns[] = new ObjectTableColumn(InternshipOrganizerMoment :: PROPERTY_END, true, $moment_alias, true);
        return $columns;
    }
}
?>