<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class SurveyContextTemplateBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_template_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function SurveyContextTemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new SurveyContextTemplateBrowserTableColumnModel();
        $renderer = new SurveyContextTemplateBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_TRUNCATE_SELECTED, Translation :: get('TruncateSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>