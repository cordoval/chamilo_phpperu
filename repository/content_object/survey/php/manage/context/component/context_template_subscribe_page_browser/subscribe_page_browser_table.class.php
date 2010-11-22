<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/subscribe_page_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/subscribe_page_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/subscribe_page_browser_table_cell_renderer.class.php';

class SurveyContextTemplateSubscribePageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_template_subscribe_page_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {

    	$model = new SurveyContextTemplateSubscribePageBrowserTableColumnModel();
        $renderer = new SurveyContextTemplateSubscribePageBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateSubscribePageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateSubscribePageBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $actions = new ObjectTableFormActions(__NAMESPACE__, SurveyContextManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE, Translation :: get('Subscribe')));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyContextManager :: PARAM_TEMPLATE_REL_PAGE_ID, $ids);
    }
}
?>