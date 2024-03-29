<?php

namespace application\assessment;

use reporting\ReportingTemplate;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use reporting\ReportingTemplateRegistration;
use repository\content_object\assessment\Assessment;
/**
 * $Id: assessment_attempts_template.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.reporting.templates
 */
/**
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../trackers/assessment_assessment_attempts_tracker.class.php';
require_once dirname(__FILE__) . '/../blocks/assessment_attempts_reporting_block.class.php';

class AssessmentAttemptsTemplate extends ReportingTemplate
{
    private $assessment;
    private $pub;

    function __construct($parent = null, $id, $params, $trail/*, $pid*/)
    {
        $this->pub = AssessmentDataManager :: get_instance()->retrieve_assessment_publication(/*$pid, */$params['assessment_publication']);
        $this->assessment = $this->pub->get_publication_object();
        $block = new AssessmentAttemptsReportingBlock($this);
        $block->set_function_parameters($params);
        $this->add_reporting_block($block);
        parent :: __construct($parent/*, $id, $params, $trail*/);
        $this->get_action_bar()->add_common_action(new ToolbarItem(Translation :: get('DeleteAllResults'), Theme :: get_common_image_path() . 'action_delete.png', $params['url'] . '&delete=aid_' . $pid, ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->assessment->get_assessment_type() == Assessment :: TYPE_ASSIGNMENT)
            $this->get_action_bar()->add_common_action(new ToolbarItem(Translation :: get('DownloadDocuments'), Theme :: get_common_image_path() . 'action_export.png', $parent->get_download_documents_url($this->pub), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
    }

	function display_context()
	{
		//publicatie, content_object, application ...
	}

	function get_application()
    {
    	return AssessmentManager::APPLICATION_NAME;
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'AssessmentAttemptsTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'AssessmentAttemptsTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //$html[] = $this->get_header();
        //$html[] = $this->get_content_object_data();
        //$html[] = $this->get_visible_reporting_blocks();
        //$html[] = $this->get_footer();

        $html[] = $this->display_header();
        $html[] = $this->get_content_object_data();
        $html[] = $this->render_all_blocks();
        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function get_content_object_data()
    {
        $assessment = $this->assessment;
        $pub = $this->pub;

        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/assessment.png);">';
        $html[] = '<div class="title">';
        $html[] = $assessment->get_title();
        $html[] = '</div>';
        $html[] = $assessment->get_description();
        $html[] = '<div class="title">';
        $html[] = Translation :: get('Statistics');
        $html[] = '</div>';
        $track = new AssessmentAssessmentAttemptsTracker();

        $avg = $track->get_average_score($pub);
        if (! isset($avg))
        {
            $avg_line = 'No results';
        }
        else
        {
            $avg_line = $avg . '%';
        }
        $html[] = Translation :: get('AverageScore') . ': ' . $avg_line;
        $html[] = '<br/>' . Translation :: get('TimesTaken') . ': ' . $track->get_times_taken($pub);
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>