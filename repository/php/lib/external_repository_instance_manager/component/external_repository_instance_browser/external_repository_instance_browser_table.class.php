<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
/**
 * $Id: external_repository_instance_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/external_repository_instance_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/external_repository_instance_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/external_repository_instance_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ExternalRepositoryInstanceBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'external_repository_instance_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ExternalRepositoryInstanceBrowserTable($browser, $parameters, $condition)
    {
        $model = new ExternalRepositoryInstanceBrowserTableColumnModel();
        $renderer = new ExternalRepositoryInstanceBrowserTableCellRenderer($browser);
        $data_provider = new ExternalRepositoryInstanceBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);

//        $action = new ObjectTableFormActions();
//
//        if (get_class($browser) == 'RepositoryManagerBrowserComponent')
//        {
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_RECYCLE_CONTENT_OBJECTS, Translation :: get('RemoveSelected')));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_MOVE_CONTENT_OBJECTS, Translation :: get('MoveSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_PUBLISH_CONTENT_OBJECT, Translation :: get('PublishSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, Translation :: get('ExportSelected'), false));
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, Translation :: get('EditSelectedRights'), false));
//
//            if ($browser->get_user()->is_platform_admin())
//            {
//                $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: ACTION_COPY_CONTENT_OBJECT_TO_TEMPLATES, Translation :: get('CopySelectedToTemplates'), false));
//            }
//
//        }
//        if (get_class($browser) == 'RepositoryManagerComplexBrowserComponent')
//        {
//            $action->add_form_action(new ObjectTableFormAction(RepositoryManager :: PARAM_ADD_OBJECTS, Translation :: get('AddObjects'), false));
//        }

        $this->set_additional_parameters($parameters);
//        $this->set_form_actions($action);
        $this->set_default_row_count(20);
    }

//    static function handle_table_action()
//    {
//        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
//        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
//    }
}
?>