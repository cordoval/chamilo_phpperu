<?php
namespace repository\content_object\adaptive_assessment;

use repository\ContentObjectDisplay;
use repository\ContentObject;
use repository\ComplexDisplay;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\Request;

require_once dirname(__FILE__) . '/adaptive_assessment_display.class.php';

/**
 * @package application.lib.weblcms.tool.adaptive_assessment.component.adaptive_assessment_viewer
 */

class AdaptiveAssessmentContentObjectDisplay
{
    const PARAM_EMBEDDED_CONTENT_OBJECT_ID = 'embedded_content_object_id';

    private $parent;

    public static function factory($parent, $type)
    {
        $class = __NAMESPACE__ . '\\AdaptiveAssessment' . Utilities :: underscores_to_camelcase($type) . 'ContentObjectDisplay';
        $file = dirname(__FILE__) . '/content_object_display/' . $type . '.class.php';

        if (file_exists($file))
        {
            require_once $file;
            return new $class($parent);
        }
        else
        {
            return new self($parent);
        }
    }

    function __construct($parent)
    {
        $this->parent = $parent;
    }

    function get_parent()
    {
        return $this->parent;
    }

    /**
     * @param ContentObject $content_object
     * @param unknown_type $adaptive_assessment_item_attempt_data
     * @param string $continue_url
     * @param string $previous_url
     * @param array $jump_urls
     */
    function display_content_object($content_object, $adaptive_assessment_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $content_object_display = ContentObjectDisplay :: factory($content_object);

        $html = array();
        $html[] = $content_object_display->get_full_html();
        $html[] = $this->add_tracking_javascript();

        return implode("\n", $html);
    }

    function add_tracking_javascript()
    {
        $trackers = $this->get_parent()->get_adaptive_assessment_trackers();
        $tracker_id = $trackers[AdaptiveAssessmentDisplayViewerComponent :: TRACKER_LEARNING_PATH_ITEM]->get_id();
        $trackers[AdaptiveAssessmentDisplayViewerComponent :: TRACKER_LEARNING_PATH_ITEM]->set_status('completed');
        $trackers[AdaptiveAssessmentDisplayViewerComponent :: TRACKER_LEARNING_PATH_ITEM]->update();

        $html[] = '<script languages="JavaScript">';
        $html[] = '    var tracker_id = ' . $tracker_id . ';';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . '/weblcms/tool/adaptive_assessment/resources/javascript/adaptive_assessment_item.js');

        return implode("\n", $html);
    }

    protected function display_link($link)
    {
        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';

        return implode("\n", $html);
    }

    protected function display_box($info)
    {
        return '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				left: 50%; right: 50%; border-width: 1px; border-style: solid;
				background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">' . $info . '</div>';
    }

    static function get_embedded_content_object_id()
    {
        if (Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION) != AdaptiveAssessmentDisplay :: ACTION_EMBED)
        {
            return Request :: get(self :: PARAM_EMBEDDED_CONTENT_OBJECT_ID);
        }
        else
        {
            return false;
        }
    }
}
?>