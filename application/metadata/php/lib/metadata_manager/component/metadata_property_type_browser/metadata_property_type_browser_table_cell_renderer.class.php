<?php
/**
 * @package metadata.tables.metadata_property_type_table
 */
require_once dirname(__FILE__).'/metadata_property_type_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/metadata_property_type_table/default_metadata_property_type_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../metadata_property_type.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataPropertyTypeBrowserTableCellRenderer extends DefaultMetadataPropertyTypeTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function MetadataPropertyTypeBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $metadata_property_type)
	{
		if ($column === MetadataPropertyTypeBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($metadata_property_type);
		}

		return parent :: render_cell($column, $metadata_property_type);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($metadata_property_type)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_metadata_property_type_url($metadata_property_type),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_metadata_property_type_url($metadata_property_type),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Associations'),
        		Theme :: get_common_image_path() . 'action_link.png',
        		$this->browser->get_edit_associations_url($metadata_property_type),
        		ToolbarItem :: DISPLAY_ICON
        ));

        return $toolbar->as_html();
	}
}
?>