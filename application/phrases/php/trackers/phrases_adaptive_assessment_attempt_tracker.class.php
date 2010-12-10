<?php
namespace application\weblcms;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use tracking\SimpleTracker;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesAdaptiveAssessmentAttemptTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_ADAPTIVE_ASSESSMENT_ID = 'adaptive_assessment_id';
    const PROPERTY_PROGRESS = 'progress';

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_adaptive_assessment_id($parameters[self :: PROPERTY_ADAPTIVE_ASSESSMENT_ID]);
        $this->set_progress($parameters[self :: PROPERTY_PROGRESS]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID,
                self :: PROPERTY_ADAPTIVE_ASSESSMENT_ID,
                self :: PROPERTY_PROGRESS));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_adaptive_assessment_id()
    {
        return $this->get_default_property(self :: PROPERTY_ADAPTIVE_ASSESSMENT_ID);
    }

    function set_adaptive_assessment_id($adaptive_assessment_id)
    {
        $this->set_default_property(self :: PROPERTY_ADAPTIVE_ASSESSMENT_ID, $adaptive_assessment_id);
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

        $condition = new EqualityCondition(PhrasesAdaptiveAssessmentItemAttemptTracker :: PROPERTY_ADAPTIVE_ASSESSMENT_VIEW_ID, $this->get_id());
        $dummy = new PhrasesAdaptiveAssessmentItemAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);

        foreach ($trackers as $tracker)
        {
            $succes &= $tracker->delete();
        }

        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>