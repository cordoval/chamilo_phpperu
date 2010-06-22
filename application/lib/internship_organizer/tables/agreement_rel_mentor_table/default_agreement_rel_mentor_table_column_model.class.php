<?php

class DefaultInternshipOrganizerAgreementRelMentorTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerAgreementRelMentorTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
    }

    private static function get_default_columns()
    {
        
        $columns = array();
        $columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_TITLE, false, InternshipOrganizerMentor :: get_table_name());
        $columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_FIRSTNAME, false, InternshipOrganizerMentor :: get_table_name());
        $columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_LASTNAME, false, InternshipOrganizerMentor :: get_table_name());
        $columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_TELEPHONE, false, InternshipOrganizerMentor :: get_table_name());
        $columns[] = new ObjectTableColumn(InternshipOrganizerMentor :: PROPERTY_EMAIL, false, InternshipOrganizerMentor :: get_table_name());
        
        return $columns;
    
    }
}
?>