<?php
namespace application\metadata;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Toolbar;
use common\libraries\Theme;
require_once dirname(__FILE__) . '/../../../tables/metadata_default_value_table/default_metadata_default_value_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class MetadataDefaultValueBrowserTableCellRenderer extends DefaultMetadataDefaultValueTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function MetadataDefaultValueBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $metadata_default_value)
	{
            if ($column === MetadataDefaultValueBrowserTableColumnModel :: get_modification_column())
            {
                    return $this->get_modification_links($metadata_default_value);
            }
            if($column->get_name() == MetadataDefaultValue :: PROPERTY_PROPERTY_ATTRIBUTE_TYPE_ID)
            {
                if($metadata_default_value->get_property_attribute_type_id() === '0') return Translation :: get('Error', null, Utilities :: COMMON_LIBRARY);
                if(!is_null($metadata_default_value->get_property_attribute_type_id()))
                {
                    return $this->browser->retrieve_metadata_property_attribute_type($metadata_default_value->get_property_attribute_type_id())->render_name();
                }
            }
            return parent :: render_cell($column, $metadata_default_value);
	}

	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($metadata_default_value)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit', null, Utilities :: COMMON_LIBRARY),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_metadata_default_value_url($metadata_default_value),
        		ToolbarItem :: DISPLAY_ICON
        ));

        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete', null, Utilities :: COMMON_LIBRARY),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_metadata_default_value_url($metadata_default_value),
        		ToolbarItem :: DISPLAY_ICON,
        		true
        ));

        

        return $toolbar->as_html();
	}
}
?>