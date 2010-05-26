<?php
/**
 * $Id: complex_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.complex_browser
 */
require_once dirname(__FILE__) . '/complex_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/complex_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/complex_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class ComplexBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'repository_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function ComplexBrowserTable($browser, $parameters, $condition, $show_subitems_column = true, $model = null, $renderer = null, $name = null)
    {
        $name = $name ? $name : (self :: DEFAULT_NAME);
        
    	if (! $model)
            $model = new ComplexBrowserTableColumnModel($browser);
        if (! $renderer)
            $renderer = new ComplexBrowserTableCellRenderer($browser, $condition);
        
        $data_provider = new ComplexBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, $name, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $action = ComplexBuilder :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM;
		if($name != self :: DEFAULT_NAME)
			$action = ComplexBuilder :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_' . $name;
				
        $actions[] = new ObjectTableFormAction($action, Translation :: get('RemoveSelected'));
       
        if($browser->show_menu())
        {
	        $action = ComplexBuilder :: PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM;
			if($name != self :: DEFAULT_NAME)
				$action = ComplexBuilder :: PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM . '_' . $name;
	        
	        $actions[] = new ObjectTableFormAction($action, Translation :: get('MoveSelected'), false);
        }
         
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>