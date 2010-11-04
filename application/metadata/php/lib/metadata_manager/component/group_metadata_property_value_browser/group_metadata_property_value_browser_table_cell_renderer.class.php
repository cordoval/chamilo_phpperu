<?php
namespace application\metadata;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Path;
use group\DefaultGroupTableCellRenderer;

require_once Path :: get_group_path() . 'lib/group_table/default_group_table_cell_renderer.class.php';


/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class GroupMetadataPropertyValueBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function GroupMetadataPropertyValueBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object)
	{
		if ($column === GroupMetadataPropertyValueBrowserTableColumnModel :: get_modification_column())
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
	private function get_modification_links($group)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

                $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_edit_group_metadata_property_values_url($group),
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