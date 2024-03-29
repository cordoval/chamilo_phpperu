<?php
namespace repository;

use common\libraries;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: repository_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/repository_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/repository_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/repository_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositoryBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new RepositoryBrowserTableColumnModel();
        $renderer = new RepositoryBrowserTableCellRenderer($browser);
        $data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RepositoryBrowserTable :: DEFAULT_NAME, $model, $renderer);

        $action = new ObjectTableFormActions(__NAMESPACE__);
        $actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_EXPORT_CP_SELECTED, Translation :: get('ExportCpSelected'), false);

        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_RECYCLE_CONTENT_OBJECTS, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));
        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_UNLINK_CONTENT_OBJECTS, Translation :: get('UnlinkSelected', null, Utilities :: COMMON_LIBRARIES)));
        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_MOVE_CONTENT_OBJECTS, Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES), false));
        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_PUBLISH_CONTENT_OBJECT, Translation :: get('PublishSelected', null, Utilities :: COMMON_LIBRARIES), false));
        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, Translation :: get('ExportSelected', null, Utilities :: COMMON_LIBRARIES), false));
        $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_CONTENT_OBJECT_SHARE_CREATOR, Translation :: get('ShareSelected', null, Utilities :: COMMON_LIBRARIES), false));
        //$action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, Translation :: get('EditSelectedRights'), false));

        if ($browser->get_repository_browser()->get_user()->is_platform_admin())
        {
            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_COPY_CONTENT_OBJECT_TO_TEMPLATES, Translation :: get('CopySelectedToTemplates'), false));
        }

        $this->set_additional_parameters($parameters);
        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);

        $action = Request :: post($class . '_action_value');
        if ($action == RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS)
        {
            Request :: set_get(RepositoryManager :: PARAM_IDENTIFIER, $ids);
        }
        else
        {
            Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
        }
    }
}
?>