<?php

require_once dirname (__FILE__) . '/share_right_column.class.php';

require_once dirname(__FILE__) . '/action_column.php';

require_once dirname(__FILE__) . '/content_object_user_share_rights_browser_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';

/**
 * Cell renderer for the content object user share rights browser
 * @author Pieterjan Broekaert
 */
class ContentObjectUserShareRightsBrowserTableCellRenderer extends ObjectTableCellRenderer
{
    private $browser;

    function ContentObjectUserShareRightsBrowserTableCellRenderer($browser)
    {
        $this->browser = $browser;
    }
    /**
     *
     * @param StaticTableColumn $column
     * @param <type> $registration
     * @return cell content
     */
    function render_cell($column, $user_share)
    {
        if ($column instanceof ShareRightColumn)
        { 
            if($user_share->has_right($column->get_right_id()))
            {   
                return Theme :: get_common_image('action_setting_true', 'png');
            }
            else
            {
                return Theme :: get_common_image('action_setting_false', 'png');
            }
        }
        else if ($column instanceof ActionColumn)
        {
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
            $toolbar->add_item(new ToolbarItem(
                            Translation :: get('ContentObjectUserShareEditor'),
                            Theme :: get_common_image_path() . 'action_edit.png',
                            $this->browser->get_content_object_share_editor_url(Request::get(RepositoryManager::PARAM_CONTENT_OBJECT_ID), $user_share->get_user_id, null),
                            ToolbarItem :: DISPLAY_ICON
            ));
            return $toolbar->as_html();
        }
        else //display the username
        {
            return UserDataManager :: get_instance()->retrieve_user($user_share->get_user_id())->get_username();
        }

    }

    function render_id_cell($user_share)
    {
        return $user_share->get_user_id();
    }

}

?>