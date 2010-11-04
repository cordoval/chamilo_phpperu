<?php
namespace application\cda;

use common\libraries\WebApplication;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * @package cda.cda_manager.component.cda_language_browser
 */
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/cda_language_browser/cda_language_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/cda_language_browser/cda_language_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/cda_language_browser/cda_language_browser_table_cell_renderer.class.php';

/**
 * Table to display a list of cda_languages
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaLanguageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'cda_language_browser_table';

    /**
     * Constructor
     */
    function CdaLanguageBrowserTable($browser, $parameters, $condition)
    {
        $model = new CdaLanguageBrowserTableColumnModel();
        $renderer = new CdaLanguageBrowserTableCellRenderer($browser);
        $data_provider = new CdaLanguageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();

        if (! $browser instanceof CdaManagerCdaLanguagesBrowserComponent)
        {
            $actions[] = new ObjectTableFormAction(CdaManager :: PARAM_DELETE_SELECTED_CDA_LANGUAGES, Translation :: get('RemoveSelected'));
        }

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>