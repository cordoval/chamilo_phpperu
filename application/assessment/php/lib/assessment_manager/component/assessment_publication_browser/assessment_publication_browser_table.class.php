<?php

namespace application\assessment;

use common\libraries\ObjectTable;
use common\libraries\Utilities;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Request;
/**
 * $Id: assessment_publication_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_publication_browser
 */
require_once dirname(__FILE__) . '/assessment_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/assessment_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../assessment_manager.class.php';

/**
 * Table to display a list of assessment_publications
 *
 * @author Sven Vanpoucke
 * @author
 */
class AssessmentPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'assessment_publication_browser_table';

    /**
     * Constructor
     */
    function AssessmentPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new AssessmentPublicationBrowserTableColumnModel();
        $renderer = new AssessmentPublicationBrowserTableCellRenderer($browser);
        $data_provider = new AssessmentPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__, AssessmentManager :: PARAM_ACTION);

        $actions->add_form_action(new ObjectTableFormAction(AssessmentManager :: ACTION_DELETE_ASSESSMENT_PUBLICATION, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
    	$ids = self :: get_selected_ids($class);
    	Request :: set_get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $ids);
    }
}
?>