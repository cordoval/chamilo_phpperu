<?php
namespace application\weblcms\tool\survey;

/**
 * $Id: survey_publisher_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_survey_publisher
 */
require_once dirname(__FILE__) . '/survey_publisher.class.php';
require_once dirname(__FILE__) . '/survey_publication_viewer.class.php';

abstract class SurveyPublisherComponent
{
    protected $parent;

    function SurveyPublisherComponent($parent)
    {
        $this->parent = $parent;
    }

    abstract function run();

    function factory($parent, $publish_action)
    {
        switch ($publish_action)
        {
            case AssessmentTool :: ACTION_VIEW :
                return new SurveyPublicationViewer($parent);
            case AssessmentTool :: ACTION_PUBLISH :
                return new SurveyPublisher($parent);
            default :
                return new SurveyPublisher($parent);
        }
    }
}
?>