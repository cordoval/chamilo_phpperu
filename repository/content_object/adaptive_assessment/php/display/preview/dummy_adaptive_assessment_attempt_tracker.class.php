<?php
namespace repository\content_object\adaptive_assessment;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class DummyAdaptiveAssessmentAttemptTracker
{

    function get_id()
    {
        return 1;
    }

    function get_progress()
    {
        return 0;
    }

    function set_progress($progress)
    {
    }

    function update()
    {
    }
}
?>