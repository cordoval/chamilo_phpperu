<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\SubManager;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentDisplayEmbedder extends SubManager
{
    const ACTION_DOCUMENT = 'document';
    const ACTION_COMPLEX_CONTENT_OBJECT = 'complex_content_object';
    const ACTION_ASSESSMENT = 'assessment';
    const ACTION_CONTENT_OBJECT = 'content_object';

    const PARAM_EMBEDDER_ACTION = 'embedder';

    const DEFAULT_ACTION = self :: ACTION_CONTENT_OBJECT;

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/embedder/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_EMBEDDER_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        self :: construct(__CLASS__, $application)->run();
    }
}
?>