<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;

use user\DefaultUserTableColumnModel;

//require_once dirname(__FILE__).'/../../../tables/agreement_rel_user_table/default_agreement_rel_user_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('user') . 'user_table/default_user_table_column_model.class.php';

class InternshipOrganizerAgreementUserBrowserTableColumnModel extends DefaultUserTableColumnModel
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
