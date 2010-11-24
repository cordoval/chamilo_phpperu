<?php
namespace repository;

use common\libraries;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\DatetimeUtilities;
use common\libraries\ComplexContentObjectSupport;

use user\UserDataManager;
use group\GroupManager;

/**
 * $Id: repository_shared_content_objects_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.shared_content_objects_browser
 */
require_once dirname(__FILE__) . '/repository_shared_content_objects_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_table/default_shared_content_objects_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositorySharedContentObjectsBrowserTableCellRenderer extends DefaultSharedContentObjectsTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === RepositorySharedContentObjectsBrowserTableColumnModel :: get_sharing_column())
        {
            return $this->get_sharing_links($content_object);
        }
        elseif ($column === RepositorySharedContentObjectsBrowserTableColumnModel :: get_rights_column())
        {
            return $this->get_rights_links($content_object);
        }

        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return '<a href="' . htmlentities($this->browser->get_type_filter_url($content_object->get_type())) . '">' . parent :: render_cell($column, $content_object) . '</a>';
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                /*if ($this->browser->has_right($content_object->get_id(), RepositoryRights :: VIEW_RIGHT))
                	return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
                else*/
                return $title_short;
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), $content_object->get_modification_date());
            case ContentObject :: PROPERTY_OWNER_ID :
                return UserDataManager :: get_instance()->retrieve_user($content_object->get_owner_id())->get_fullname();
            case Translation :: get('SharedWith') :
                return $this->get_shared_users_groups($content_object);

        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_sharing_links($content_object)
    {
        $user = $this->browser->get_user();

        if ($user->get_id() != $content_object->get_owner_id())
        {
            $toolbar = new Toolbar();
            $right = $content_object->get_optional_property('user_right');
            if (! $right)
            {
                $right = $content_object->get_optional_property('group_right');
            }

            if ($right >= ContentObjectShare :: VIEW_RIGHT)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('View', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible.png', $this->browser->get_content_object_viewing_url($content_object), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('ViewNotAvailable', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

            if ($right >= ContentObjectShare :: USE_RIGHT)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->browser->get_publish_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('PublishNotAvailable', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

            if ($right >= ContentObjectShare :: REUSE_RIGHT)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('ReUse', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_reuse.png', $this->browser->get_copy_content_object_url($content_object->get_id(), $this->browser->get_user_id()), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('ReUseNotAvailable', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_reuse_na.png', null, ToolbarItem :: DISPLAY_ICON));
            }

            if ($content_object instanceof ComplexContentObjectSupport)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_build.png', $this->browser->get_browse_complex_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON));
            }

            return $toolbar->as_html();
        }
        else
        {
            return null;
        }
    }

    private function get_rights_links($content_object)
    {
        $user = $this->browser->get_user();

        if ($user->get_id() == $content_object->get_owner_id())
        {
            $toolbar = new Toolbar();
            $toolbar->add_item(new ToolbarItem(Translation :: get('EditShareRights'), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_content_object_share_browser_url($content_object->get_id()), ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unshare', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_unshare.png', $this->browser->get_content_object_share_deleter_url($content_object->get_id(), null), ToolbarItem :: DISPLAY_ICON));
            return $toolbar->as_html();
        }
        else
        {
            return null;
        }
    }

    private function get_shared_users_groups($content_object)
    {
        $shared_users = $content_object->get_shared_users();
        $shared_groups = $content_object->get_shared_groups();

        $html = array();
        $html[] = '<select>';

        foreach ($shared_users as $user_id => $user_name)
        {
            $html[] = '<option value="u_' . $user_id . '">' . $user_name . '</option>';
        }

        if (count($shared_users) > 0 && count($shared_groups) > 0)
        {
            $html[] = '<option disabled="disabled">---------------------</option>';
        }

        foreach ($shared_groups as $group_id => $group_name)
        {
            $html[] = '<option value="g_' . $group_id . '">[' . strtoupper(Translation :: get('GroupShort', null, GroupManager :: APPLICATION_NAME)) . '] ' . $group_name . '</option>';
        }

        $html[] = '</select>';
        return implode("\n", $html);
    }
}
?>