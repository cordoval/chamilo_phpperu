<?php 
namespace repository\content_object\survey;

use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyContextTemplateRelPageTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_context_template_rel_page_browser_table';

   
    function __construct($browser, $parameters, $condition)
    {
        
    
    	$model = new SurveyContextTemplateRelPageTableColumnModel();
        $renderer = new SurveyContextTemplateRelPageTableCellRenderer($browser);
        $data_provider = new SurveyContextTemplateRelPageTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyContextTemplateRelPageTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
              
//        $actions = array();
//        
//        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);
        
        $this->set_form_actions($actions);
    }
 
}
?>