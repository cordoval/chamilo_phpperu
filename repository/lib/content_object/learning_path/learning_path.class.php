<?php
/**
 * $Id: learning_path.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.learning_path
 */
class LearningPath extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_allowed_types()
    {
        return array(LearningPath :: get_type_name(), LearningPathItem :: get_type_name());
    }

    const PROPERTY_CONTROL_MODE = 'control_mode';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_PATH = 'path';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CONTROL_MODE, self :: PROPERTY_VERSION, self :: PROPERTY_PATH);
    }

    function get_control_mode()
    {
        return unserialize($this->get_additional_property(self :: PROPERTY_CONTROL_MODE));
    }

    function set_control_mode($control_mode)
    {
        if (! is_array($control_mode))
            $control_mode = array($control_mode);

        $this->set_additional_property(self :: PROPERTY_CONTROL_MODE, serialize($control_mode));
    }

    function get_version()
    {
        return $this->get_additional_property(self :: PROPERTY_VERSION);
    }

    function set_version($version)
    {
        $this->set_additional_property(self :: PROPERTY_VERSION, $version);
    }

    function get_path()
    {
        return $this->get_additional_property(self :: PROPERTY_PATH);
    }

    function set_path($path)
    {
        $this->set_additional_property(self :: PROPERTY_PATH, $path);
    }

    function get_full_path()
    {
        return Path :: get(SYS_SCORM_PATH) . $this->get_owner_id() . '/' . $this->get_path() . '/';
    }
}
?>