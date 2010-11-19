<?php
namespace application\metadata;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;

use repository\DefaultContentObjectTableCellRenderer;

require_once dirname(__FILE__) . '/../../../../../../../repository/php/lib/content_object_table/default_content_object_table_cell_renderer.class.php';


/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */

class ContentObjectMetadataPropertyValueBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function __construct($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $content_object)
	{
		if ($column === ContentObjectMetadataPropertyValueBrowserTableColumnModel :: get_modification_column())
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
        		Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_edit_content_object_metadata_property_values_url($content_object),
        		ToolbarItem :: DISPLAY_ICON
        ));

        return $toolbar->as_html();
	}
}
?>