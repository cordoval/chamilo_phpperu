<?php
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
    function RepositoryBrowserTable($browser, $parameters, $condition)
    {
        $model = new RepositoryBrowserTableColumnModel();
        $renderer = new RepositoryBrowserTableCellRenderer($browser);
        $data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, RepositoryBrowserTable :: DEFAULT_NAME, $model, $renderer);
        if (get_class($browser) == 'RepositoryManagerBrowserComponent')
        {
            $actions = array();
            /*$actions[RepositoryManager :: PARAM_RECYCLE_SELECTED] = Translation :: get('RemoveSelected');
			$actions[RepositoryManager :: PARAM_MOVE_SELECTED] = Translation :: get('MoveSelected');
			$actions[RepositoryManager :: PARAM_PUBLISH_SELECTED] = Translation :: get('PublishSelected');*/
            
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_RECYCLE_CONTENT_OBJECTS, Translation :: get('RemoveSelected'));
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_MOVE_CONTENT_OBJECTS, Translation :: get('MoveSelected'), false);
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_PUBLISH_CONTENT_OBJECT, Translation :: get('PublishSelected'), false);
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_EXPORT_CONTENT_OBJECTS, Translation :: get('ExportSelected'), false);
            $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_EDIT_CONTENT_OBJECT_RIGHTS, Translation :: get('EditSelectedRights'), false);
            
            if ($browser->get_user()->is_platform_admin())
            {
                $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_COPY_CONTENT_OBJECT_TO_TEMPLATES, Translation :: get('CopySelectedToTemplates'), false);
            }
        
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
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>