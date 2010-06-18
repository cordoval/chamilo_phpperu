<?php

require_once dirname(__FILE__) . '/subscribe_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_location_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_location_browser_table_cell_renderer.class.php';

class InternshipOrganizerSubscribeLocationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subcribe_location_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerSubscribeLocationBrowserTable($browser, $parameters, $condition)
    {
         	
    	$model = new InternshipOrganizerSubscribeLocationBrowserTableColumnModel();
        $renderer = new InternshipOrganizerSubscribeLocationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerSubscribeLocationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerSubscribeLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>