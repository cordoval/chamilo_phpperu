<?php
/**
 * $Id: system_announcement.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.system_announcement
 */
/**
 * This class represents a system announcement
 */
class SystemAnnouncement extends ContentObject implements Versionable
{
    const PROPERTY_ICON = 'icon';

    const ICON_CONFIRMATION = 1;
    const ICON_ERROR = 2;
    const ICON_WARNING = 3;
    const ICON_STOP = 4;
    const ICON_QUESTION = 5;
    const ICON_CONFIG = 6;
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

    function get_icon_name()
    {
        switch ($this->get_icon())
        {
            case self :: ICON_CONFIRMATION :
                $icon = 'confirmation';
                break;
            case self :: ICON_ERROR :
                $icon = 'error';
                break;
            case self :: ICON_WARNING :
                $icon = 'warning';
                break;
            case self :: ICON_STOP :
                $icon = 'stop';
                break;
            case self :: ICON_QUESTION :
                $icon = 'question';
                break;
            case self :: ICON_CONFIG :
                $icon = 'config';
                break;
        }

        return 'system_announcement_' . $icon;
    }

    static function get_possible_icons()
    {
        $icons = array();

        $icons[self :: ICON_CONFIRMATION] = Translation :: get('Confirmation');
        $icons[self :: ICON_ERROR] = Translation :: get('Error');
        $icons[self :: ICON_WARNING] = Translation :: get('Warning');
        $icons[self :: ICON_STOP] = Translation :: get('Stop');
        $icons[self :: ICON_QUESTION] = Translation :: get('Question');
        $icons[self :: ICON_CONFIG] = Translation :: get('Config');

        return $icons;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ICON);
    }
}
?>