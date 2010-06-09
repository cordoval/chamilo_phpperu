<?php
/**
 * $Id: link_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../link_table/default_link_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LinkBrowserTableCellRenderer extends DefaultLinkTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LinkBrowserTableCellRenderer($browser, $type)
    {
        parent :: __construct($browser, $type);
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $object)
    {
        if ($column === LinkBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($object);
        }

        return parent :: render_cell($column, $object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($object)
    {
        $toolbar = new Toolbar();
        
        $link_id = $this->render_id_cell($object);
        
        if($this->type == LinkBrowserTable :: TYPE_INCLUDES)
        {
        	return '&nbsp';
        }
        
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_delete_link_url($this->type, $this->browser->get_object()->get_id(), $link_id),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
		));
        return $toolbar->as_html();
        
    }
}
?>