<?php

require_once dirname(__FILE__) . '/subscribe_page_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_page_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_page_browser_table_cell_renderer.class.php';

class SurveyContextTemplateSubscribePageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'subcribe_page_browser_table';

    /**
     * Constructor
     */
    function SurveyContextTemplateSubscribePageBrowserTable($browser, $parameters, $condition)
    {
         	
    	$model = new SurveyContextTemplateSubscribePageBrowserTableColumnModel();
        $renderer = new SurveyContextTemplateSubscribePageBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateSubscribePageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateSubscribePageBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerCategoryManager :: PARAM_SUBSCRIBE_SELECTED, Translation :: get('Subscribe'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>