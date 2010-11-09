<?php

namespace application\context_linker;

use common\libraries\ToolbarItem;
use common\libraries\Translation;
use repository\DefaultContentObjectTableCellRenderer;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Utilities;

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

            $toolbar->add_item(new ToolbarItem(
                            Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                            Theme :: get_common_image_path() . 'action_edit.png',
                            $this->browser->get_browse_context_links_url($content_object),
                            ToolbarItem :: DISPLAY_ICON
            ));

            return $toolbar->as_html();
	}
}
?>