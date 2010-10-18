<?php

require_once dirname(__FILE__) . '/../../../tables/period_rel_group_table/default_period_rel_group_table_column_model.class.php';

class InternshipOrganizerPeriodRelGroupBrowserTableColumnModel extends DefaultInternshipOrganizerPeriodRelGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodRelGroupBrowserTableColumnModel($browser)
    {
        parent :: __construct();
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
