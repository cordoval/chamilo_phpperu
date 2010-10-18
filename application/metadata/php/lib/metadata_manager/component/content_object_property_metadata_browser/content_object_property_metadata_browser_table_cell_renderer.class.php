<?php
/**
 * @package metadata.tables.content_object_property_metadata_table
 */
require_once dirname(__FILE__).'/content_object_property_metadata_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/content_object_property_metadata_table/default_content_object_property_metadata_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../content_object_property_metadata.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectPropertyMetadataBrowserTableCellRenderer extends DefaultContentObjectPropertyMetadataTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function ContentObjectPropertyMetadataBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object_property_metadata)
	{

            if($column->get_name() == ContentObjectPropertyMetadata :: PROPERTY_PROPERTY_TYPE_ID)
            {
                $metadata_property_type = $this->browser->retrieve_metadata_property_type($content_object_property_metadata->get_property_type_id());
                return $metadata_property_type->get_ns_prefix() . ':' . $metadata_property_type->get_name();
            }

            if ($column === ContentObjectPropertyMetadataBrowserTableColumnModel :: get_modification_column())
            {
                    return $this->get_modification_links($content_object_property_metadata);
            }

            return parent :: render_cell($column, $content_object_property_metadata);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($content_object_property_metadata)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_content_object_property_metadata_url($content_object_property_metadata),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_content_object_property_metadata_url($content_object_property_metadata),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));

        return $toolbar->as_html();
	}
}
?>