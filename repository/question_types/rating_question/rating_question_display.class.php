<?php
/**
 * $Id: rating_question_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.rating_question
 */
/**
 * This class can be used to display open questions
 */
class RatingQuestionDisplay extends ContentObjectDisplay
{
	function get_description()
	{
		$question =  $this->get_content_object();
		$min = $question->get_low();
        $max = $question->get_high();
        
        
        
        $html = array();
        $html[] = parent :: get_description();
        $html[] = '<select class="rating_slider">';
        $html[] = '</option>';
		for($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
            $html[] = '<option value="'.$i.'" > '.$i.'';
            $html[] = '</option>';
        }
        $html[] = '</select>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/rating_question.js');
		return implode("\n", $html);
	}
}
?>