<?php
/**
 * $Id: survey_attempts_template.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.reporting.templates
 */
/**
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/../../trackers/survey_participant_tracker.class.php';

class SurveyQuestionAnswersTemplate extends ReportingTemplate
{
    private $survey;
    private $pub;

    function SurveyQuestionAnswersTemplate($parent = null, $id, $params, $trail, $pid)
    {
        $this->pub = SurveyDataManager :: get_instance()->retrieve_survey_publication($pid);
        $this->survey = $this->pub->get_publication_object();
        
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("SurveyQuestionAnswers"), array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
        
        parent :: __construct($parent, $id, $params, $trail);
        
        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('DeleteAllResults'), Theme :: get_common_image_path() . 'action_delete.png', $params['url'] . '&delete=aid_' . $pid, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
    //        if ($this->survey->get_survey_type() == Survey :: TYPE_ASSIGNMENT)
    //        {
    //            $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('DownloadDocuments'), Theme :: get_common_image_path() . 'action_export.png', $parent->get_download_documents_url($this->pub), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
    //	     }
    }

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'SurveyAttemptsTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'SurveyAttemptsTemplateDescription';
        
        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        //template header
        $html[] = $this->get_header();
        //$html[] = '<div class="reporting_center">';
        //show visible blocks
        

        $html[] = $this->get_content_object_data();
        
        $html[] = $this->get_visible_reporting_blocks();
        //$html[] = '</div>';
        //template footer
        $html[] = $this->get_footer();
        
        return implode("\n", $html);
    }

    function get_content_object_data()
    {
        $survey = $this->survey;
        $pub = $this->pub;
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/survey.png);">';
        $html[] = '<div class="title">';
        $html[] = $survey->get_title();
        $html[] = '</div>';
        $html[] = $survey->get_description();
        $html[] = '<div class="title">';
        $html[] = Translation :: get('Statistics');
        $html[] = '</div>';
        $track = new SurveyParticipantTracker();
        
        $user_count = $pub->get_user_count();
        $participants = $track->count_participants($pub);
        if ($user_count !== 0)
        {
            $participation = ($participants / $user_count) * 100;
        }
        else
        {
            $participation = 0;
        }
        
        $html[] = '<br/>' . Translation :: get('Participation') . ': ' . $participation.' %';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
?>