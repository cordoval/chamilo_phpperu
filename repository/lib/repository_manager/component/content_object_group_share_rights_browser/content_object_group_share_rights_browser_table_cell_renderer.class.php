<?php

require_once dirname(__FILE__) . '/../content_object_user_share_rights_browser/share_right_column.class.php';

require_once dirname(__FILE__) . '/../content_object_user_share_rights_browser/action_column.php';

require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_column_model.class.php';
require_once Path :: get_group_path() . 'lib/group_table/default_group_table_cell_renderer.class.php';

/**
 * Cell renderer for the content object Group share rights browser
 * @author Pieterjan Broekaert
 */
class ContentObjectGroupShareRightsBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{

    /**
     *
     * @param StaticTableColumn $column
     * @param <type> $registration
     * @return cell content
     */
    function render_cell($column, $group)
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
        {
            switch ($column->get_name())
            {
                case Group :: PROPERTY_DESCRIPTION :
                    $description = strip_tags(parent :: render_cell($column, $group));
                    return Utilities :: truncate_string($description);
                case Translation :: get('Users') :
                    return $group->count_users();
                case Translation :: get('Subgroups') :
                    return $group->count_subgroups(true);
            }
            return parent :: render_cell($column, $group);
        }
    }

}

?>