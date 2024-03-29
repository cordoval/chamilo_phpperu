<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Utilities;

/**
 * $Id: recycle_bin_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.recycle_bin_browser
 */
require_once dirname(__FILE__) . '/recycle_bin_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_table_cell_renderer.class.php';
/**
 * Cell renderer for the recycle bin browser table
 */
class RecycleBinBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The recycle bin browser component in which the learning objects will be
     * displayed.
     */
    private $browser;
    /**
     * Array acting as a cache for learning object titles
     */
    private $parent_title_cache;

    /**
     * Constructor
     * @param RepositoryManagerRecycleBinBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->parent_title_cache = array();
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === RecycleBinBrowserTableColumnModel :: get_action_column())
        {
            return $this->get_action_links($content_object);
        }
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
            case ContentObject :: PROPERTY_PARENT_ID :
                $pid = $content_object->get_parent_id();
                if (! isset($this->parent_title_cache[$pid]))
                {
                    $category = RepositoryDataManager :: get_instance()->retrieve_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $pid))->next_result();

                    $this->parent_title_cache[$pid] = '<a href="' . htmlentities($this->browser->get_url(array(RepositoryManager :: PARAM_CATEGORY_ID => $pid, RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS))) . '" title="' . htmlentities(Translation :: get('BrowseThisCategory')) . '">' . ($category ? $category->get_name() : Translation :: get('Root', null, Utilities :: COMMON_LIBRARIES)) . '</a>';
                }
                return $this->parent_title_cache[$pid];
        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_action_links($content_object)
    {
        $toolbar  = new Toolbar();

		$toolbar->add_item(new ToolbarItem(
       		Translation :: get('Restore', null, Utilities :: COMMON_LIBRARIES),
       		Theme :: get_common_image_path().'action_restore.png',
			$this->browser->get_content_object_restoring_url($content_object),
			ToolbarItem :: DISPLAY_ICON
		));

	   	$toolbar->add_item(new ToolbarItem(
        	Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
     		Theme :: get_common_image_path().'action_delete.png',
			$this->browser->get_content_object_deletion_url($content_object),
			ToolbarItem :: DISPLAY_ICON,
			true
		));
		return $toolbar->as_html();

    }
}
?>