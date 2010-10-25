<?php
/**
 * $Id: survey_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../survey_content_object_publisher.class.php';

/**
 * Represents the repo_viewer component for the survey tool.
 */
class SurveyToolPublisherComponent extends SurveyTool
{

    /**
     * Shows the html for this component.
     *
     */
    function run()
    {
        ToolComponent :: launch($this);
    }
}

?>