<?php namespace repository\content_object\survey;

use common\libraries\Translation;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;
use common\libraries\SimpleTable;
use repository\RepositoryDataManager;


require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyPageConfigTable extends SimpleTable
{
    const DEFAULT_NAME = 'survey_page_config_table';


    function __construct($browser, $parameters, $page_id)
    {


    	$model = new SurveyPageConfigTableColumnModel();
        $renderer = new SurveyPageConfigTableCellRenderer($browser);
//        $data_provider = new SurveyPageConfigTableDataProvider($browser, $condition);
        $configs = $page = RepositoryDataManager::get_instance()->retrieve_content_object($page_id)->get_config();
//        dump($configs);
        parent :: __construct($configs, $renderer, $actionhandler = null, self :: DEFAULT_NAME);
//        parent :: __construct($data_provider, SurveyPageConfigTable :: DEFAULT_NAME, $model, $renderer);
//        $this->set_additional_parameters($parameters);

        $actions = array();

//        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);

//        $this->set_form_actions($actions);
    }

}
?>