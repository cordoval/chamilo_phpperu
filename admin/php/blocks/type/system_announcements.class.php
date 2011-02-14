<?php

namespace admin;

use repository\ContentObject;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use common\libraries\Redirect;
use common\libraries\StringUtilities;
use repository\content_object\system_announcement\SystemAnnouncement;

/**
 * @package admin.block
 * 
 * $Id: system_announcements.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
require_once CoreApplication :: get_application_class_path('admin') . 'blocks/admin_block.class.php';

class AdminSystemAnnouncements extends AdminBlock {

    public static function get_default_image_path($application='', $type='', $size = Theme :: ICON_MEDIUM) {
        if ($type) {
            return parent::get_default_image_path($application, $type, $size);
        } else {
            return Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(SystemAnnouncement :: get_type_name())) . 'logo/' . $size . '.png';
        }
    }

    function is_visible() {
        if ($this->is_empty() && !$this->show_when_empty()) {
            return false;
        }

        return true; //i.e.display on homepage when anonymous
    }

    /* function is_editable()
      {
      return false;
      } */

    function is_hidable() {
        return false;
    }

    function is_deletable() {
        return false;
    }

    function show_when_empty() {
        $configuration = $this->get_configuration();
        $result = isset($configuration['show_when_empty']) ? $configuration['show_when_empty'] : true;
        $result = (bool) $result;
        return $result;
    }

    function is_empty() {
        $announcements = $this->get_announcements();
        return $announcements->size() == 0;
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    function get_icon() {
        return self::get_default_image_path();
    }

    private $_announcements = null;

    function get_announcements() {
        if (!is_null($this->_announcements)) {
            return $this->_announcements;
        }
        return $this->_announcements = AdminDataManager :: get_instance()->retrieve_system_announcement_publications();
    }

    function get_announcement_link($announcement) {
        $paremeters = array();
        $parameters[Application :: PARAM_ACTION] = AdminManager :: ACTION_VIEW_SYSTEM_ANNOUNCEMENT;
        $parameters[AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID] = $announcement->get_id();

        $result = Redirect :: get_link(AdminManager :: APPLICATION_NAME, $parameters, null, null, Redirect :: TYPE_CORE);
        return $result;
    }

    function display_content() {
        $announcements = $this->get_announcements();

        if ($announcements->size() == 0) {
            return htmlspecialchars(Translation :: get('NoSystemAnnouncementsCurrently'));
        }

        $html = array();
        $html[] = '<ul style="list-style: none; margin: 0px; padding: 0px;">';

        while ($announcement = $announcements->next_result()) {
            if ($announcement->is_visible_for_target_users()) {
                $object = $announcement->get_publication_object();
                $icon = htmlspecialchars(Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type()))) . 'logo/' . $object->get_icon_name(Theme :: ICON_MINI) . '.png';
                $href = $this->get_announcement_link($announcement);
                $title = htmlspecialchars($object->get_title()) ;
                $target = $this->get_view() == self::WIDGET_VIEW ? ' target="_blank" ' : '';
                $html[] = '<li style="margin-bottom: 2px;"><img style="vertical-align: middle;" src="' . $icon . '"/>&nbsp;&nbsp;<a href="' . $href . '"'. $target .'>' . $title. '</a></li>';
            }
        }

        $html[] = '</ul>';

        return implode(StringUtilities::NEW_LINE, $html);
    }

}

?>