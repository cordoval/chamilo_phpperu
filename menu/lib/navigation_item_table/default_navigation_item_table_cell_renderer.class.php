<?php
/**
 * $Id: default_navigation_item_table_cell_renderer.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib.navigation_item_table
 */

// TODO: Add functionality to menu item so it "knows" whether it's the first or the last item



class DefaultNavigationItemTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultNavigationItemTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param MenuManagerTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $menu_publication The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $navigation_item)
    {
        switch ($column->get_name())
        {
            case NavigationItem :: PROPERTY_TITLE :
                return $navigation_item->get_title();
            case 'Type':
            	if($navigation_item->get_is_category())
            	{
            		$icon = 'category.png';
            	}
            	else
            	{
            		$icon = 'document.png';
            	}
            	return '<img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $icon . '" />';
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>