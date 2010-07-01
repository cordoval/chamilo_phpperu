<?php
/**
 * @package application.lib.weblcms.trackers
 */
class WeblcmsLpAttemptTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_LP_ID = 'lp_id';
    const PROPERTY_PROGRESS = 'progress';

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_course_id($parameters[self :: PROPERTY_COURSE_ID]);
        $this->set_lp_id($parameters[self :: PROPERTY_LP_ID]);
        $this->set_progress($parameters[self :: PROPERTY_PROGRESS]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_PROGRESS));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    function set_course_id($course_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course_id);
    }

    function get_lp_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_ID);
    }

    function set_lp_id($lp_id)
    {
        $this->set_default_property(self :: PROPERTY_LP_ID, $lp_id);
    }

    function get_progress()
    {
        return $this->get_default_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_default_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function delete()
    {
        $succes = parent :: delete();

        $condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $this->get_id());
        $dummy = new WeblcmsLpiAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $succes &= $tracker->delete();
        }

        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>