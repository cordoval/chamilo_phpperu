<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/category_table/default_category_table_column_model.class.php';

/**
 * Table column model for the user browser table
 */
class InternshipOrganizerCategoryBrowserTableColumnModel extends DefaultInternshipOrganizerCategoryTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerCategoryBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Locations')));
        $this->add_column(new StaticTableColumn(Translation :: get('Subcategories')));
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