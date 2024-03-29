<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTable;
use common\libraries\ObjectTableFormAction;

/**
 * $Id: repository_shared_content_objects_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.shared_content_objects_browser
 */
require_once dirname(__FILE__) . '/repository_shared_content_objects_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/repository_shared_content_objects_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/repository_shared_content_objects_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class RepositorySharedContentObjectsBrowserTable extends ObjectTable
{

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new RepositorySharedContentObjectsBrowserTableColumnModel($browser);
        $renderer = new RepositorySharedContentObjectsBrowserTableCellRenderer($browser);
        $data_provider = new RepositorySharedContentObjectsBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, Utilities :: get_classname_from_namespace(__CLASS__, true), $model, $renderer);
        if ($browser instanceof RepositoryManagerBrowserComponent)
        {
            $actions = array();
            //$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_RECYCLE_SELECTED, Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES));
            //$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_MOVE_SELECTED, Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES), false);
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_PUBLISH_CONTENT_OBJECT, Translation :: get('PublishSelected', null, Utilities :: COMMON_LIBRARIES), false);
        }

        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>