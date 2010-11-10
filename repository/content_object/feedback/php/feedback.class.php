<?php
namespace repository\content_object\feedback;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\AttachmentSupport;
use common\libraries\Theme;

use repository\ContentObject;

/**
 * $Id: feedback.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.feedback
 */
/**
 * A feedback
 */
class Feedback extends ContentObject implements Versionable, AttachmentSupport
{
    const PROPERTY_ICON = 'icon';

    const ICON_THUMBS_UP = 1;
    const ICON_THUMBS_DOWN = 2;
    const ICON_WRONG = 3;
    const ICON_RIGHT = 4;
    const ICON_INFORMATIVE = 5;

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    function get_icon()
    {
        return $this->get_additional_property(self :: PROPERTY_ICON);
    }

    function set_icon($icon)
    {
        return $this->set_additional_property(self :: PROPERTY_ICON, $icon);
    }

    function get_icon_name($size = Theme :: ICON_SMALL)
    {
        switch ($this->get_icon())
        {
            case self :: ICON_THUMBS_UP :
                $icon = 'thumbs_up';
            case self :: ICON_THUMBS_DOWN :
                $icon = 'thumbs_down';
            case self :: ICON_RIGHT :
                $icon = 'right';
            case self :: ICON_WRONG :
                $icon = 'wrong';
            case self :: ICON_INFORMATIVE :
                $icon = 'informative';
        }

        return $size . '_' . $icon;
    }

    static function get_possible_icons()
    {
        $icons[self :: ICON_THUMBS_UP] = Translation :: get('thumbs_up');
        $icons[self :: ICON_THUMBS_DOWN] = Translation :: get('thumbs_down');
        $icons[self :: ICON_WRONG] = Translation :: get('wrong');
        $icons[self :: ICON_RIGHT] = Translation :: get('right');
        $icons[self :: ICON_INFORMATIVE] = Translation :: get('informative');
        return $icons;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ICON);
    }
}
?>