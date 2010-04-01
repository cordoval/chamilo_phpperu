<?php

require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../location.class.php';

class InternshipOrganizerLocationBrowserTableColumnModel extends DefaultInternshipOrganizerLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerLocationBrowserTableColumnModel()
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