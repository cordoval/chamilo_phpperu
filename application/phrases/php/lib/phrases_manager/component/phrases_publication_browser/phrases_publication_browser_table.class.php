<?php
namespace application\phrases;

use common\libraries\ObjectTable;
use common\libraries\Utilities;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Translation;
use common\libraries\Request;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'phrases_publication_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new PhrasesPublicationBrowserTableColumnModel();
        $renderer = new PhrasesPublicationBrowserTableCellRenderer($browser);
        $data_provider = new PhrasesPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = new ObjectTableFormActions(__NAMESPACE__, PhrasesManager :: PARAM_ACTION);

        $actions->add_form_action(new ObjectTableFormAction(PhrasesManager :: ACTION_DELETE_PHRASES_PUBLICATION, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(PhrasesManager :: PARAM_PHRASES_PUBLICATION, $ids);
    }
}
?>