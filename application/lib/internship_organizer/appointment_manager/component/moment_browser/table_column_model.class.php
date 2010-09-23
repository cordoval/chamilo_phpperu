<?php

require_once dirname(__FILE__) . '/../../../tables/moment_table/default_moment_table_column_model.class.php';

class InternshipOrganizerMomentRelUserBrowserTableColumnModel extends DefaultInternshipOrganizerMomentTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerMomentRelUserBrowserTableColumnModel()
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