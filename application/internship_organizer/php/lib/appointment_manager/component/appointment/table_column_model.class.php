<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/appointment_table/default_appointment_table_column_model.class.php';

class InternshipOrganizerAppointmentBrowserTableColumnModel extends DefaultInternshipOrganizerAppointmentTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerAppointmentBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>