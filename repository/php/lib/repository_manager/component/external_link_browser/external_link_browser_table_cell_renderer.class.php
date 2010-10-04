<?php
/**
 * $Id: external_link_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.link_browser
 */
require_once dirname(__FILE__) . '/external_link_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../external_link_table/default_external_link_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ExternalLinkBrowserTableCellRenderer extends DefaultExternalLinkTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ExternalLinkBrowserTableCellRenderer($browser)
    {
        parent :: __construct($browser);
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $object)
    {
        if ($column === ExternalLinkBrowserTableColumnModel :: get_modification_column())
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
        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('View'),
        			Theme :: get_common_image_path().'action_details.png', 
					$this->browser->get_external_repository_object_viewing_url($object),
				 	ToolbarItem :: DISPLAY_ICON
		));
        return $toolbar->as_html();
    
    }
}
?>