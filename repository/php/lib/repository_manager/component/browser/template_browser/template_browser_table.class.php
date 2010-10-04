<?php
/**
 * $Id: template_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.template_browser
 */
require_once dirname(__FILE__) . '/template_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/template_browser_table_column_model.class.php';
/**
 * Table to display a set of learning objects.
 */
class TemplateBrowserTable extends RepositoryBrowserTable
{

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function TemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new TemplateBrowserTableColumnModel();
        $renderer = new TemplateBrowserTableCellRenderer($browser);
        $data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
        parent :: ObjectTable($data_provider, Utilities :: camelcase_to_underscores(__CLASS__), $model, $renderer);
        
        $actions = array();
        $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_DELETE_TEMPLATE, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(RepositoryManager :: ACTION_COPY_CONTENT_OBJECT_FROM_TEMPLATES, Translation :: get('CopySelectedToRepository'), false);
        $this->set_form_actions($actions);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
    
	static function handle_table_action()
    {
        $ids = self :: get_selected_ids(Utilities :: camelcase_to_underscores(__CLASS__));
        Request :: set_get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $ids);
    }
}
?>