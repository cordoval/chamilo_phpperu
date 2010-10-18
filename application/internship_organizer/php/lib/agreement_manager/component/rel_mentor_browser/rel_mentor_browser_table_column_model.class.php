<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/agreement_rel_mentor_table/default_agreement_rel_mentor_table_column_model.class.php';

class InternshipOrganizerAgreementRelMentorBrowserTableColumnModel extends DefaultInternshipOrganizerAgreementRelMentorTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerAgreementRelMentorBrowserTableColumnModel($browser)
    {
        parent :: __construct();
        //		$this->add_column(self :: get_modification_column());
    }

    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new ObjectTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
