<?php
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
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function RepositorySharedContentObjectsBrowserTable($browser, $parameters, $condition)
    {
        $model = new RepositorySharedContentObjectsBrowserTableColumnModel();
        $renderer = new RepositorySharedContentObjectsBrowserTableCellRenderer($browser);
        $data_provider = new RepositorySharedContentObjectsBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RepositorySharedContentObjectsBrowserTable :: DEFAULT_NAME, $model, $renderer);
        if (get_class($browser) == 'RepositoryManagerBrowserComponent')
        {
            $actions = array();
            //$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_RECYCLE_SELECTED, Translation :: get('RemoveSelected'));
            //$actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_MOVE_SELECTED, Translation :: get('MoveSelected'), false);
            $actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_PUBLISH_SELECTED, Translation :: get('PublishSelected'), false);
        }
        if (get_class($browser) == 'RepositoryManagerComplexBrowserComponent')
        {
            $actions = array();
            $actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_ADD_OBJECTS, Translation :: get('AddObjects'), false);
        }
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>