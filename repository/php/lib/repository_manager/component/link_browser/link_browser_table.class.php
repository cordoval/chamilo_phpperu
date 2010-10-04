<?php
/**
 * $Id: link_browser_table.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/link_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/link_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class LinkBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'link_browser_table';

    private $type;
    
    const TYPE_PUBLICATIONS = 1;
    const TYPE_PARENTS = 2;
    const TYPE_CHILDREN = 3;
    const TYPE_ATTACHMENTS = 4;
    const TYPE_INCLUDES = 5;
    
    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function LinkBrowserTable($browser, $parameters, $condition, $type)
    { 
        $this->type = $type;
        
    	$model = new LinkBrowserTableColumnModel($type);
        $renderer = new LinkBrowserTableCellRenderer($browser, $type);
        $data_provider = new LinkBrowserTableDataProvider($browser, $condition, $type);
        parent :: __construct($data_provider, LinkBrowserTable :: DEFAULT_NAME . '_' . $type, $model, $renderer);
        
        $this->set_additional_parameters($parameters);
        $this->set_default_row_count(20);
    }
    
	/**
     * ContentObjectPublicationAttributes not directly extracted from the
     * database but preprocessed and are therefore not returned by the datamanager
     * as a resultset. It is instead an array which means we have to overwrite
     * this method to handle it accordingly.
     */
    function get_objects($offset, $count, $order_column)
    {
        if($this->type == self :: TYPE_PUBLICATIONS)
        {
	    	$objects = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
	        $table_data = array();
	        $column_count = $this->get_column_model()->get_column_count();
	        foreach ($objects as $object)
	        {
	            $row = array();
	            if ($this->has_form_actions())
	            {
	                $row[] = $object->get_publication_object_id();
	            }
	            for($i = 0; $i < $column_count; $i ++)
	            {
	                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
	            }
	            $table_data[] = $row;
	        }
	        return $table_data;
        }
        else
        {
        	return parent :: get_objects($offset, $count, $order_column);
        }
    }
}
?>