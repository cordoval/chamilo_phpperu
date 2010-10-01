<?php

require_once dirname (__FILE__) . '/share_right_column.class.php';

require_once dirname(__FILE__) . '/action_column.php';

require_once dirname(__FILE__) . '/content_object_user_share_rights_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';

/**
 * Cell renderer for the content object user share rights browser
 * @author Pieterjan Broekaert
 */
class ContentObjectUserShareRightsBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{

    /**
     *
     * @param StaticTableColumn $column
     * @param <type> $registration
     * @return cell content
     */
    function render_cell($column, $user)
    {
        if ($column instanceof ShareRightColumn)
        { // to do return right value:
            return Theme :: get_common_image('action_setting_false', 'png');
        }
        else if ($column instanceof ActionColumn)
        {
            return 'edit_link';
        }
        else
            return parent :: render_cell ($column, $user);
    }

}

?>