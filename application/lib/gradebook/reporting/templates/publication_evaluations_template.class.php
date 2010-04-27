<?php
require_once dirname(__FILE__) . '/../blocks/publication_evaluations_reporting_block.class.php';

class PublicationEvaluationsTemplate extends ReportingTemplate
{	 
	function PublicationEvaluationsTemplate($parent)
	{
		parent :: __construct($parent);
		$this->add_reporting_block(new PublicationEvaluationsReportingBlock($this));
	}
	
//	function to_html()
//	{
//        $html[] = $this->display_header();
//        $html[] = $this->get_content_object_data();
//        $html[] = $this->render_blocks();
//        $html[] = $this->display_footer();
//        
//        return implode("\n", $html);
//	}
//
//    function get_content_object_data()
//    {
//        $assessment = $this->assessment;
//        $pub = $this->pub;
//        
//        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/assessment.png);">';
//        $html[] = '<div class="title">';
//        $html[] = $assessment->get_title();
//        $html[] = '</div>';
//        $html[] = $assessment->get_description();
//        $html[] = '<div class="title">';
//        $html[] = Translation :: get('Statistics');
//        $html[] = '</div>';
//        $track = new AssessmentAssessmentAttemptsTracker();
//        
//        $avg = $track->get_average_score($pub);
//        if (! isset($avg))
//        {
//            $avg_line = 'No results';
//        }
//        else
//        {
//            $avg_line = $avg . '%';
//        }
//        $html[] = Translation :: get('AverageScore') . ': ' . $avg_line;
//        $html[] = '<br/>' . Translation :: get('TimesTaken') . ': ' . $track->get_times_taken($pub);
//        $html[] = '</div>';
//        
//        return implode("\n", $html);
//    }
	
	public function display_context()
	{
  
	}
	
	function get_application()
	{
		return GradebookManager::APPLICATION_NAME;
	}
} 
?>