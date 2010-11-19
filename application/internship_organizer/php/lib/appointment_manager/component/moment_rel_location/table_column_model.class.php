<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\StaticTableColumn;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/moment_rel_location_table/default_moment_rel_location_table_column_model.class.php';

class InternshipOrganizerMomentRelLocationBrowserTableColumnModel extends DefaultInternshipOrganizerMomentRelLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
        $this->add_column(new StaticTableColumn(Translation :: get('Appointments')));
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