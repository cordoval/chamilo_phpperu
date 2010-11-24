<?php
namespace application\internship_organizer;


require_once dirname(__FILE__) . '/../../../tables/mentor_rel_location_table/default_mentor_rel_location_table_column_model.class.php';

class oldInternshipOrganizerMentorRelLocationBrowserTableColumnModel extends DefaultInternshipOrganizerMentorRelLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerMentorRelLocationBrowserTableColumnModel($browser)
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
