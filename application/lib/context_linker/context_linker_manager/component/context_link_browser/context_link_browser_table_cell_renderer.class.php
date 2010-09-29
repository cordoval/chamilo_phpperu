<?php
/**
 * @package context_linker.tables.context_link_table
 */
require_once dirname(__FILE__).'/context_link_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/context_link_table/default_context_link_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../context_link.class.php';
require_once dirname(__FILE__).'/../../context_linker_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContextLinkBrowserTableCellRenderer extends DefaultContextLinkTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function ContextLinkBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $context_link)
	{

            if ($column === ContextLinkBrowserTableColumnModel :: get_modification_column())
            {
                    return $this->get_modification_links($context_link);
            }
            elseif($column->get_name() == DefaultContextLinkTableColumnModel :: COLUMN_TYPE)
            {
                return Theme :: get_content_object_image($context_link[ContentObject :: PROPERTY_TYPE]);

            }
            elseif($column->get_name() == DefaultContextLinkTableColumnModel :: COLUMN_TITLE)
            {
                return $context_link[ContentObject :: PROPERTY_TITLE];
            }
            elseif($column->get_name() == DefaultContextLinkTableColumnModel :: COLUMN_METADATA_PROPERTY_TYPE)
            {
                return $context_link[MetadataPropertyType :: PROPERTY_NS_PREFIX] . ':' . $context_link[MetadataPropertyType :: PROPERTY_NAME];
            }
            elseif($column->get_name() == DefaultContextLinkTableColumnModel :: COLUMN_METADATA_PROPERTY_VALUE)
            {
                return $context_link[MetadataPropertyValue :: PROPERTY_VALUE];
            }

            return parent :: render_cell($column, $context_link);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($context_link)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_context_link_url($context_link),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_context_link_url($context_link),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));

        return $toolbar->as_html();
	}
}
?>