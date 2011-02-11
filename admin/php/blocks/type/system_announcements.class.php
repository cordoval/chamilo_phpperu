<?php
namespace admin;

use repository\ContentObject;

use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use common\libraries\Redirect;
use repository\content_object\system_announcement\SystemAnnouncement;
/**
 * @package admin.block
 * $Id: system_announcements.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
require_once CoreApplication :: get_application_class_path('admin') . 'blocks/admin_block.class.php';

class AdminSystemAnnouncements extends AdminBlock
{

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(ContentObject:: get_content_object_type_namespace(SystemAnnouncement:: get_type_name())) . 'logo/' . $size . '.png';
        }
    }

        function display_header() {
        $html = array();

        $icon = self::get_default_image_path();
        $html[] = '<div class="block" id="block_' . $this->get_block_info()->get_id() . '" style="background-image: url(' . $icon . ');">';
        $html[] = $this->display_title();
        $html[] = '<div class="description"' . ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') . '>';

        return implode("\n", $html);
    }

    function is_visible()
    {
        return true; //i.e.display on homepage when anonymous
    }
    
    function as_html()
    {
        $configuration = $this->get_configuration();
        $show_when_empty = $configuration['show_when_empty'];
        $html = array();

        $announcements = AdminDataManager :: get_instance()->retrieve_system_announcement_publications();

        if ($announcements->size() > 0)
        {
            $html[] = $this->display_header();
            $html[] = $this->get_system_announcements($announcements);
            $html[] = $this->display_footer();
        }
        else
        {
            if ($show_when_empty)
            {
                $html[] = $this->display_header();
                $html[] = Translation :: get('NoSystemAnnouncementsCurrently');
                $html[] = $this->display_footer();
            }
        }

        return implode("\n", $html);
    }

    function get_system_announcements($announcements)
    {
        $html = array();

        $html[] = '<ul style="list-style: none; margin: 0px; padding: 0px;">';

        if ($announcements->size() == 0)
        {
            $html[] = '<li style="margin-bottom: 2px;">' . htmlspecialchars(Translation :: get('NoNewSystemAnnouncements')) . '</a></li>';
        }

        while ($announcement = $announcements->next_result())
        {
            if ($announcement->is_visible_for_target_users())
            {
                $object = $announcement->get_publication_object();
                $html[] = '<li style="margin-bottom: 2px;"><img style="vertical-align: middle;" src="' . htmlspecialchars(Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type()))) . 'logo/' . $object->get_icon_name(Theme :: ICON_MINI) . '.png" />&nbsp;&nbsp;<a href="' . 
                    htmlspecialchars(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(
                        Application :: PARAM_ACTION => AdminManager :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT,
                        AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID => $announcement->get_id()), null, null, Redirect :: TYPE_CORE)) . '">' . htmlspecialchars($object->get_title()) . '</a></li>';
            }
        }

        $html[] = '</ul>';

        return implode("\n", $html);
    }

    /*function is_editable()
	{
		return false;
	}*/

    function is_hidable()
    {
        return false;
    }

    function is_deletable()
    {
        return false;
    }
}
?>