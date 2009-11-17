<?php
/**
 * $Id: fill_in_blanks_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class FillInBlanksQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        $title = $data['title'];
        
        //description may not be in prompt, but in a <p> tag, maybe even with an embedded blockquote
        $description = parent :: get_tag_content('prompt');
        $description = parent :: import_images($description);
        //$descr = $data['itemBody']['prompt'];
        /*if ($descr == null)
			$descr = $data['itemBody']['p'];

		if ($data['itemBody']['blockquote'] != null)
		{
			$descr .= $data['itemBody']['blockquote']['_content'];
		}*/
        $question = new FillInBlanksQuestion();
        $question->set_title($title);
        $question->set_description($description);
        
        $answer_text = parent :: get_tag_content('itemBody');
        $pos = strpos($answer_text, '</prompt>') + 9;
        
        $answer_text = substr($answer_text, $pos, strlen($answer_text) - $pos);
        $answers = $data['responseDeclaration'];
        dump($answers);
        
        foreach ($answers as $answer)
        {
            if (! is_array($answer))
                continue;
            
            $value = $answer['correctResponse']['value'];
            $value = '[' . $value . ']';
            $answer_text = preg_replace('/\<textentryinteraction(.*)textentryinteraction\>/i', $value, $answer_text, 1);
        }
        
        if (is_null($answer_text))
            $answer_text = '';
        
        $question->set_answer_text($answer_text);
        $question->set_question_type(0);
        
        $this->create_answers($data, $question);
        parent :: create_question($question);
        return $question->get_id();
    }

    function create_answers($data, $question)
    {
        $answers = $data['responseDeclaration'];
        
        foreach ($answers as $i => $answer)
        {
            if (! is_array($answer))
                continue;
                
            //$answer_list[$answer['identifier']] = $answer['correctResponse']['value'];
            $value = $answer['correctResponse']['value'];
            $weight = $answer['correctResponse']['mapping']['mapEntry']['mappedValue'];
            if ($weight = '')
                $weight = 1;
            
            $comment = parent :: get_tag_content('feedbackInline', array('outcomeIdentifier' => 'FEEDBACK' . $i));
            
            //$answer_lo = $this->create_answer($answer['correctResponse']['value']);
            //$this->create_complex_answer($question, $answer_lo, 1);
            $fib_ans = new FillInBlanksQuestionAnswer('[' . $value . ']', $weight, $comment, 20);
            $question->add_answer($fib_ans);
        }
    }
}
?>