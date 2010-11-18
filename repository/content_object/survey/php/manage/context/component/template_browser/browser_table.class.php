<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';

class SurveyContextTemplateBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'template_browser_table';


    function SurveyContextTemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new SurveyContextTemplateBrowserTableColumnModel();
        $renderer = new SurveyContextTemplateBrowserTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);

        $actions = new ObjectTableFormActions(__NAMESPACE__, SurveyContextManager :: PARAM_ACTION);
        $actions->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_DELETE_CONTEXT_TEMPLATE, Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES)));
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);

    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(SurveyContextManager :: PARAM_CONTEXT_TEMPLATE_ID, $ids);
    }
}
?>