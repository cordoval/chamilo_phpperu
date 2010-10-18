<?php
/**
 * @package metadata.tables.metadata_property_attribute_type_table
 */
require_once dirname(__FILE__).'/metadata_property_attribute_type_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/metadata_property_attribute_type_table/default_metadata_property_attribute_type_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../metadata_property_attribute_type.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataPropertyAttributeTypeBrowserTableCellRenderer extends DefaultMetadataPropertyAttributeTypeTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function MetadataPropertyAttributeTypeBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $metadata_property_attribute_type)
	{
		switch($column->get_name())
                {
                    case MetadataPropertyAttributeType :: PROPERTY_VALUE_TYPE:
                        switch($metadata_property_attribute_type->get_value_type())
                        {
                            case MetadataPropertyAttributeType :: VALUE_TYPE_NONE:
                                return '-';
                            case MetadataPropertyAttributeType :: VALUE_TYPE_ID:
                                return Translation :: get('attribute');
                            case MetadataPropertyAttributeType :: VALUE_TYPE_VALUE:
                                return Translation :: get('value');
                        }
                      case MetadataPropertyAttributeType :: PROPERTY_VALUE:
                          switch($metadata_property_attribute_type->get_value_type())
                        {
                            case MetadataPropertyAttributeType :: VALUE_TYPE_ID:
                                $attribute_type = $this->browser->retrieve_metadata_property_attribute_type($metadata_property_attribute_type->get_value());
                                $prefix = (is_null($attribute_type->get_ns_prefix())) ? '' : $attribute_type->get_ns_prefix() . ':';
                                $value = (is_null($attribute_type->get_value())) ? '' : '=' . $attribute_type->get_value();

                                return $prefix . $attribute_type->get_name() . $value;
                             default:
                                return $metadata_property_attribute_type->get_value();
                        }
                }

                if ($column === MetadataPropertyAttributeTypeBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($metadata_property_attribute_type);
		}

		return parent :: render_cell($column, $metadata_property_attribute_type);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($metadata_property_attribute_type)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_metadata_property_attribute_type_url($metadata_property_attribute_type),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_metadata_property_attribute_type_url($metadata_property_attribute_type),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));

        return $toolbar->as_html();
	}
}
?>