<?php

require_once dirname(__FILE__) . '/../../../tables/region_table/default_region_table_column_model.class.php';

/**
 * Table column model for the user browser table
 */
class InternshipOrganizerRegionBrowserTableColumnModel extends DefaultInternshipOrganizerRegionTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerRegionBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Subregions')));
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