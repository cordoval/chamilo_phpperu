<?php

require_once dirname(__FILE__) . '/../../../tables/moment_rel_location_table/default_moment_rel_location_table_column_model.class.php';

class InternshipOrganizerMomentRelLocationBrowserTableColumnModel extends DefaultInternshipOrganizerMomentRelLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerMomentRelLocationBrowserTableColumnModel()
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