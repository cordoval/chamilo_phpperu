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
    const DEFAULT_NAME = 'template_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function TemplateBrowserTable($browser, $parameters, $condition)
    {
        $model = new TemplateBrowserTableColumnModel();
        $renderer = new TemplateBrowserTableCellRenderer($browser);
        $data_provider = new RepositoryBrowserTableDataProvider($browser, $condition);
        parent :: ObjectTable($data_provider, TemplateBrowserTable :: DEFAULT_NAME, $model, $renderer);
        
        $actions = array();
        $actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_DELETE_TEMPLATES, Translation :: get('RemoveSelected'));
        $actions[] = new ObjectTableFormAction(RepositoryManager :: PARAM_COPY_FROM_TEMPLATES, Translation :: get('CopySelectedToRepository'), false);
        $this->set_form_actions($actions);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
}
?>