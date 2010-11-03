<?php
namespace application\internship_organizer;


require_once dirname(__FILE__) . '/../../../tables/organisation_table/default_organisation_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../organisation.class.php';

class InternshipOrganizerOrganisationBrowserTableColumnModel extends DefaultInternshipOrganizerOrganisationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function InternshipOrganizerOrganisationBrowserTableColumnModel()
    {
        parent :: __construct();
        //$this->set_default_order_column(0);
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Locations')));
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