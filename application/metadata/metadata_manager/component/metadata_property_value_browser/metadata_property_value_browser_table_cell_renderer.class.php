<?php
/**
 * @package metadata.tables.metadata_property_value_table
 */
require_once dirname(__FILE__).'/metadata_property_value_browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../metadata_property_value.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataPropertyValueBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function MetadataPropertyValueBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object)
	{
		if ($column === MetadataPropertyValueBrowserTableColumnModel :: get_modification_column())
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

                $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_edit_metadata_property_values_url($content_object),
        		ToolbarItem :: DISPLAY_ICON
        ));

//        $toolbar->add_item(new ToolbarItem(
//        		Translation :: get('Delete'),
//        		Theme :: get_common_image_path() . 'action_delete.png',
//        		$this->browser->get_delete_metadata_property_value_url($metadata_property_value),
//        		ToolbarItem :: DISPLAY_ICON,
//        		true
//        ));

        return $toolbar->as_html();
	}
}
?>