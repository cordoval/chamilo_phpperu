<?php
/**
 * $Id: doubles_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.doubles_browser
 */

require_once dirname(__FILE__) . '/../../../../content_object_table/default_content_object_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class DoublesBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function DoublesBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === DoublesBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
 
    	switch ($column->get_name())
        {
        	case 'Duplicates';
        		return $content_object->get_content_hash();
        }
        
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>