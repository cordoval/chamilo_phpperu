<?php

require_once dirname(__FILE__) . '/rel_page_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_page_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_page_browser_table_cell_renderer.class.php';

class SurveyContextTemplateRelPageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'template_rel_page_browser_table';

   
    function SurveyContextTemplateRelPageBrowserTable($browser, $parameters, $condition)
    {
        
    
    	$model = new SurveyContextTemplateRelPageBrowserTableColumnModel();
        $renderer = new SurveyContextTemplateRelPageBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateRelPageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateRelPageBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);
        
        $this->set_form_actions($actions);
    }
 
}
?>