<?php
/**
 * $Id: forum_post_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ForumPostBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ForumPostBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    // Inherited
    function render_cell($column, $cloi)
    {
     	if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($cloi);
        }
        
    	switch ($column->get_name())
        {
            case Translation :: get('AddDate') :
                return $cloi->get_add_date();
        }
        
        return parent :: render_cell($column, $cloi);
    }
    
 	function get_modification_links($cloi)
    {
        return parent :: get_modification_links($cloi, array(), true);
    }
}
?>