<?php
/**
 * $Id: help_item_browser_table_cell_renderer.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component.help_item_browser_table
 */
require_once dirname(__FILE__) . '/help_item_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../help_item_table/default_help_item_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class HelpItemBrowserTableCellRenderer extends DefaultHelpItemTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function HelpItemBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $help_item)
    {
        if ($column === HelpItemBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($help_item);
        }
        
        return parent :: render_cell($column, $help_item);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($help_item)
    {
    	$toolbar = new Toolbar(); 
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->browser->get_url(array(Application :: PARAM_ACTION => HelpManager :: ACTION_UPDATE_HELP_ITEM, HelpManager :: PARAM_HELP_ITEM => $help_item->get_id())),
    			ToolbarItem :: DISPLAY_ICON
    	));
        
        return $toolbar->as_html();
    }
}
?>