<?php
namespace repository\content_object\forum;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\DatetimeUtilities;
use common\libraries\Path;
use common\libraries\ToolbarItem;
use repository\ComplexBrowserTableCellRenderer;
use repository\ComplexBrowserTableColumnModel;

/**
 * $Id: forum_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ForumBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ForumBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    // Inherited
    function render_cell($column, $complex_content_object_item)
    {
        if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($complex_content_object_item)->as_html();
        }

        switch ($column->get_name())
        {
            case Translation :: get('AddDate') :
                return DatetimeUtilities :: format_locale_date(null, $complex_content_object_item->get_add_date());
        }

        return parent :: render_cell($column, $complex_content_object_item);
    }

    function get_modification_links($complex_content_object_item)
    {
        $toolbar = parent :: get_modification_links($complex_content_object_item, true);
        $array = array();
        if ($complex_content_object_item->get_type() == 1)
        {
        	$toolbar->add_item(new ToolbarItem(
            			Translation :: get('UnSticky'),
            			Theme :: get_common_image_path().'action_remove_sticky.png',
    					$this->browser->get_complex_content_object_item_sticky_url($complex_content_object_item),
    				 	ToolbarItem :: DISPLAY_ICON
    		));

        	$toolbar->add_item(new ToolbarItem(
            			Translation :: get('ImportantNa'),
            			Theme :: get_common_image_path().'action_make_important_na.png',
    					null,
    				 	ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            if ($complex_content_object_item->get_type() == 2)
            {
            	$toolbar->add_item(new ToolbarItem(
                			Translation :: get('StickyNa'),
                			Theme :: get_common_image_path().'action_make_sticky_na.png',
        					null,
        				 	ToolbarItem :: DISPLAY_ICON
        		));

            	$toolbar->add_item(new ToolbarItem(
                			Translation :: get('UnImportant'),
                			Theme :: get_common_image_path().'action_remove_important.png',
        					$this->browser->get_complex_content_object_item_important_url($complex_content_object_item),
        				 	ToolbarItem :: DISPLAY_ICON
        		));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(
                			Translation :: get('MakeSticky'),
                			Theme :: get_common_image_path().'action_make_sticky.png',
        					$this->browser->get_complex_content_object_item_sticky_url($complex_content_object_item),
        				 	ToolbarItem :: DISPLAY_ICON
        		));

            	$toolbar->add_item(new ToolbarItem(
                			Translation :: get('MakeImportant'),
                			Theme :: get_common_image_path().'action_make_important.png',
        					$this->browser->get_complex_content_object_item_important_url($complex_content_object_item),
        				 	ToolbarItem :: DISPLAY_ICON
        		));
            }
        }

        return $toolbar;
    }
}
?>