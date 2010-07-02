<?php

require_once dirname(__FILE__) . '/../../../tables/category_rel_period_table/default_category_rel_period_table_column_model.class.php';

class InternshipOrganizerCategoryRelPeriodBrowserTableColumnModel extends DefaultInternshipOrganizerCategoryRelPeriodTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerCategoryRelPeriodBrowserTableColumnModel($browser)
    {
        parent :: __construct();
//        $this->add_column(self :: get_modification_column());
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
