<?php
/**
 * $Id: feedback.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.feedback
 */
/**
 * A feedback
 */
class Feedback extends ContentObject
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
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
    
    function get_icon()
    {
        return $this->get_additional_property(self :: PROPERTY_ICON);
    }

    function set_icon($icon)
    {
        return $this->set_additional_property(self :: PROPERTY_ICON, $icon);
    }

    function supports_attachments()
    {
        return true;
    }

    function get_icon_name()
    {
        switch ($this->get_icon())
        {
            case self :: ICON_THUMBS_UP :
                return 'thumbs_up';
            case self :: ICON_THUMBS_DOWN :
                return 'thumbs_down';
            case self :: ICON_RIGHT :
                return 'right';
            case self :: ICON_WRONG :
                return 'wrong';
            case self :: ICON_INFORMATIVE :
                return 'informative';
        }
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