<?php

require_once dirname(__FILE__) . '/rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_user_browser_table_cell_renderer.class.php';

class InternshipOrganizerOrganisationRelUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rel_user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerOrganisationRelUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerOrganisationRelUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerOrganisationRelUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerOrganisationRelUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerOrganisationRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>