<?php
namespace repository\content_object\survey;

use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyUserTable extends ObjectTable
{
   
    const DEFAULT_NAME = 'survey_user_table';

    /**
     * Constructor
     */
    function __construct($component, $parameters, $condition)
    {

        $model = new SurveyUserTableColumnModel($component);
        $renderer = new SurveyUserTableCellRenderer($component);
        $data_provider = new SurveyUserTableDataProvider($component, $condition);
        parent :: __construct($data_provider, SurveyUserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);

        $action = new ObjectTableFormActions(__NAMESPACE__, SurveyContextManager :: PARAM_ACTION);
	   	$action->add_form_action(new ObjectTableFormAction(SurveyContextManager :: ACTION_DELETE_TEMPLATE_USER, Translation :: get('Delete'), true));

        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
       	Request :: set_get(SurveyContextManager :: PARAM_TEMPLATE_USER_ID, $ids);
    }
}
?>