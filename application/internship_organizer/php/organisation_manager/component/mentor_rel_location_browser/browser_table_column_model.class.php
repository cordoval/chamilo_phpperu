<?php

require_once dirname(__FILE__) . '/../../../tables/mentor_rel_location_table/default_mentor_rel_location_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/region_table/default_region_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../mentor_rel_location.class.php';

class InternshipOrganizerMentorRelLocationBrowserTableColumnModel extends DefaultInternshipOrganizerMentorRelLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerMentorRelLocationBrowserTableColumnModel()
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