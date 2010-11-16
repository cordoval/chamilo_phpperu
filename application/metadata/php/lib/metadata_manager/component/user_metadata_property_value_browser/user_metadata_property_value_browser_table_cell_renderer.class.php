<?php
namespace application\metadata;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Path;
use common\libraries\Utilities;

use user\DefaultUserTableCellRenderer;
use common\libraries\Utilities;

require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';


/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class UserMetadataPropertyValueBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function UserMetadataPropertyValueBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object)
	{
		if ($column === UserMetadataPropertyValueBrowserTableColumnModel :: get_modification_column())
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
	private function get_modification_links($user)
	{
		$toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

                $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_edit_user_metadata_property_values_url($user),
        		ToolbarItem :: DISPLAY_ICON
        ));

        return $toolbar->as_html();
	}
}
?>