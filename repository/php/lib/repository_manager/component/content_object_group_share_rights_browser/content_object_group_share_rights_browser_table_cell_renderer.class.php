<?php
namespace repository;

use user\UserManager;

use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\ObjectTableCellRenderer;

use group\GroupDataManager;
use group\Group;
use group\GroupManager;

require_once dirname(__FILE__) . '/../content_object_user_share_rights_browser/share_right_column.class.php';
require_once dirname(__FILE__) . '/../content_object_user_share_rights_browser/action_column.php';
require_once dirname(__FILE__) . '/content_object_group_share_rights_browser_table_column_model.class.php';
require_once Path :: get_group_path() . 'lib/group_table/default_group_table_cell_renderer.class.php';

/**
 * Cell renderer for the content object Group share rights browser
 * @author Pieterjan Broekaert
 */
class ContentObjectGroupShareRightsBrowserTableCellRenderer extends ObjectTableCellRenderer
{

    private $browser;

    function __construct($browser)
    {
        $this->browser = $browser;
    }

    /**
     *
     * @param StaticTableColumn $column
     * @param <type> $registration
     * @return cell content
     */
    function render_cell($column, $group_share)
    {
        $group = GroupDataManager :: get_instance()->retrieve_group($group_share->get_group_id());

        if ($column instanceof ShareRightColumn)
        { // to do return right value:
            if ($group_share->has_right($column->get_right_id()))
            {
                return Theme :: get_common_image('action_setting_true', 'png');
            }
            else
            {
                return Theme :: get_common_image('action_setting_false', 'png');
            }
        }
        else
            if ($column instanceof ActionColumn)
            {
                $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
                $toolbar->add_item(new ToolbarItem(Translation :: get('ContentObjectGroupShareEditor'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_content_object_share_editor_url(Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID), null, $group_share->get_group_id()), ToolbarItem :: DISPLAY_ICON));
                $toolbar->add_item(new ToolbarItem(Translation :: get('ContentObjectGroupShareDeleter'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_content_object_share_deleter_url(Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID), null, $group_share->get_group_id()), ToolbarItem :: DISPLAY_ICON));
                return $toolbar->as_html();
            }
            else
            {
                switch ($column->get_name())
                {
                    case Group :: PROPERTY_NAME :
                        return $group->get_name();
                    case Translation :: get('Users', null, UserManager :: APPLICATION_NAME) :
                        return $group->count_users(true);
                    case Translation :: get('Subgroups', null, GroupManager :: APPLICATION_NAME) :
                        return $group->count_subgroups(true);
                }
            }
    }

    function render_id_cell($object)
    {
        return $object->get_group_id();
    }

}

?>