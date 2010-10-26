<?php
namespace application\weblcms;

/**
 * $Id: weblcms_course_changes_tracker.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.trackers
 */
class WeblcmsCourseChangesTracker extends ChangesTracker
{
    const CLASS_NAME = __CLASS__;

    // Can be used for subscribsion of users / classes
    const PROPERTY_TARGET_REFERENCE_ID = 'target_reference_id';

    /**
     * Get the default properties of all aggregate trackers.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TARGET_REFERENCE_ID));
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function validate_parameters(array $parameters = array())
    {
        parent :: validate_parameters($parameters);

        if ($parameters[self :: PROPERTY_TARGET_REFERENCE_ID])
        {
            $this->set_target_reference_id($parameters[self :: PROPERTY_TARGET_REFERENCE_ID]);
        }
        else
        {
            $this->set_target_reference_id(0);
        }
    }

    public function get_target_reference_id()
    {
        return $this->get_default_property(self :: PROPERTY_TARGET_REFERENCE_ID);
    }

    public function set_target_reference_id($target_reference_id)
    {
        $this->set_default_property(self :: PROPERTY_TARGET_REFERENCE_ID, $target_reference_id);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
        //return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>