<?php
namespace application\internship_organizer;

use common\libraries\StaticTableColumn;

require_once dirname(__FILE__) . '/../../../tables/user_table/default_user_table_column_model.class.php';

class InternshipOrganizerPeriodAgreementUserBrowserTableColumnModel extends DefaultInternshipOrganizerUserTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

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
