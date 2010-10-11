<?php
/**
 * $Id: assessment_open_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentOpenQuestionResultDisplay extends QuestionResultDisplay
{

    function display_question_result()
    {
        $question = $this->get_question();
        $type = $question->get_question_type();
        $answers = $this->get_answers();

        $html = array();

        switch ($type)
        {
            case AssessmentOpenQuestion :: TYPE_OPEN :
                $this->display_open($html, $answers[0]);
                break;
            case AssessmentOpenQuestion :: TYPE_OPEN_WITH_DOCUMENT :
                $this->display_open($html, $answers[0]);
                $this->display_document_box($html, $answers[2], true);
                break;
            case AssessmentOpenQuestion :: TYPE_DOCUMENT :
                $this->display_document_box($html, $answers[2]);
                break;
        }

        return implode("\n", $html);
    }

    function add_borders()
    {
        return true;
    }

    function display_open(&$html, $answer)
    {
        $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none;">';
        $html[] = Translation :: get('Answer');
        $html[] = '</div>';

        $html[] = '<br />';

        if ($answer && $answer != '')
            $html[] = $answer;
        else
            $html[] = Translation :: get('NoAnswer');

        $html[] = '<br />';
    }

	function display_document_box(&$html, $answer, $with_open = false)
    {
        if ($with_open)
        {
            $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none; border-top: 1px solid #B5CAE7;">';
        }
        else
        {
            $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none;">';
        }

        $html[] = Translation :: get('Document');
        $html[] = '</div>';
        
    	if (! $answer)
        {
            
        	$html[] = '<br /><p>' . Translation :: get('NoDocument') . '</p><div class="clear"></div><br />';
            return;
        }

        $document = RepositoryDataManager :: get_instance()->retrieve_content_object($answer, Document :: get_type_name());

        $html[] = '<br />';

        $html[] = '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				  left: 50%; right: 50%; border-width: 1px; border-style: solid;
				  background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">';

        $html[] = sprintf(Translation :: get('LPDownloadDocument'), $document->get_filename(), $document->get_filesize());
        $html[] .= '<br /><a target="about:blank" href="' . RepositoryManager :: get_document_downloader_url($document->get_id()) . '">' . Translation :: get('Download') . '</a>';

        $html[] = '</div>';
        $html[] = '<br />';
    }
}
?>