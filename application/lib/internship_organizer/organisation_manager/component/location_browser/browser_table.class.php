<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipOrganizerLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'location_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerLocationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerOrganisationManager :: PARAM_DELETE_SELECTED_LOCATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>