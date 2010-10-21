<?php
/**
 * @package context_linker.tables.content_object_table
 */
require_once dirname(__FILE__).'/content_object_browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../context_linker_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function ContentObjectBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object)
	{
            if ($column === ContentObjectBrowserTableColumnModel :: get_modification_column())
            {
                    return $this->get_modification_links($content_object);
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
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

//        $toolbar->add_item(new ToolbarItem(
//        		Translation :: get('Edit'),
//        		Theme :: get_common_image_path() . 'action_edit.png',
//        		$this->browser->get_update_context_link_url($context_link),
//        		ToolbarItem :: DISPLAY_ICON
//        ));

            $toolbar->add_item(new ToolbarItem(
                            Translation :: get('Edit'),
                            Theme :: get_common_image_path() . 'action_edit.png',
                            $this->browser->get_browse_context_links_url($content_object),
                            ToolbarItem :: DISPLAY_ICON
            ));

            return $toolbar->as_html();
	}
}
?>