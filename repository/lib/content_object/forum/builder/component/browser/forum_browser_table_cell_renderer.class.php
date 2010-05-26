<?php
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
            return $this->get_modification_links($complex_content_object_item);
        }

        switch ($column->get_name())
        {
            case Translation :: get('AddDate') :
                return $complex_content_object_item->get_add_date();
        }

        return parent :: render_cell($column, $complex_content_object_item);
    }

    function get_modification_links($complex_content_object_item)
    {
        $array = array();
        if ($complex_content_object_item->get_type() == 1)
        {
            $array[] = array('href' => $this->browser->get_complex_content_object_item_sticky_url($complex_content_object_item), 'label' => Translation :: get('UnSticky'), 'img' => Theme :: get_common_image_path() . 'action_remove_sticky.png');
            $array[] = array('label' => Translation :: get('ImportantNa'), 'img' => Theme :: get_common_image_path() . 'action_make_important_na.png');
        }
        else
            if ($complex_content_object_item->get_type() == 2)
            {
                $array[] = array('label' => Translation :: get('StickyNa'), 'img' => Theme :: get_common_image_path() . 'action_make_sticky_na.png');
                $array[] = array('href' => $this->browser->get_complex_content_object_item_important_url($complex_content_object_item), 'label' => Translation :: get('UnImportant'), 'img' => Theme :: get_common_image_path() . 'action_remove_important.png');
            }
            else
            {
                $array[] = array('href' => $this->browser->get_complex_content_object_item_sticky_url($complex_content_object_item), 'label' => Translation :: get('MakeSticky'), 'img' => Theme :: get_common_image_path() . 'action_make_sticky.png');
                $array[] = array('href' => $this->browser->get_complex_content_object_item_important_url($complex_content_object_item), 'label' => Translation :: get('MakeImportant'), 'img' => Theme :: get_common_image_path() . 'action_make_important.png');
            }
        return parent :: get_modification_links($complex_content_object_item, $array, true);
    }
}
?>