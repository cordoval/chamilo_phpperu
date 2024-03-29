<?php

namespace application\reservations;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
/**
 * $Id: default_item_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.item_table
 */

/**
 * TODO: Add comment
 */
class DefaultItemTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct($browser)
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $item)
    {
        if ($name = $column->get_name())
        {
            switch ($name)
            {
                case Item :: PROPERTY_ID :
                    return $item->get_id();
                case Item :: PROPERTY_NAME :
                    return $item->get_name();
                case Item :: PROPERTY_DESCRIPTION :
                    $description = strip_tags($item->get_description());
                    if (strlen($description) > 175)
                    {
                        $description = mb_substr($description, 0, 170) . '&hellip;';
                    }
                    return '<div style="word-wrap: break-word; max-width: 250px;" >' . $description . '</div>';
                case Item :: PROPERTY_RESPONSIBLE :
                    //$user = UserDataManager :: get_instance()->retrieve_user($item->get_responsible());
                    //return $user->get_fullname();
                    return $item->get_responsible();
                case Item :: PROPERTY_CREDITS :
                    return $item->get_credits() . ' ' . Translation :: get('PerHour');
            }
        }

        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_common_image_path() . 'treemenu_types/document.png';
            return '<img src="' . $img . '"alt="' . Utilities :: get_classname_from_object($item, true) . '" />';
        }

        return '&nbsp;';
    }

    function render_id_cell($item)
    {
        return $item->get_id();
    }
}
?>