<?php
/**
 * $Id: matching_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class MatchingQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        
        $question = new MatchingQuestion();
        $title = $data['title'];
        //$descr = $data['itemBody']['matchInteraction']['prompt'];
        $description = parent :: get_tag_content('prompt');
        $question->set_title($title);
        $question->set_description($this->import_images($description));
        $this->create_answers($data, $question);
        parent :: create_question($question);
        return $question->get_id();
    }

    function create_answers($data, $question)
    {
        //get matching and scores
        $matchvalues = $data['responseDeclaration']['correctResponse']['value'];
        foreach ($matchvalues as $matchvalue)
        {
            $parts = split(' ', $matchvalue);
            $matches[$parts[0]]['match'] = $parts[1];
            //dump($matches);
        }
        
        $matchscores = $data['responseDeclaration']['mapping']['mapEntry'];
        foreach ($matchscores as $matchscore)
        {
            $parts = split(' ', $matchscore['mapKey']);
            $matches[$parts[0]]['score'] = $matchscore['mappedValue'];
            if (! isset($matches[$parts[0]]['match']))
                $matches[$parts[0]]['match'] = $parts[1];
            
        //dump($matches);
        }
        
        //get actual answers
        $matchsets = $data['itemBody']['matchInteraction']['simpleMatchSet'];
        foreach ($matchsets as $matchset)
        {
            //dump($matchset);
            $answers = $matchset['simpleAssociableChoice'];
            foreach ($answers as $answer)
            {
                //dump($answer);
                //$starttag = '<simpleAssociableChoice identifier="'.$answer['identifier'].'" matchMax="'.$answer['matchMax'].'">';
                //$endtag = '</simpleAssociableChoice>';
                $text = $this->get_tag_content('simpleAssociableChoice', array('identifier' => $answer['identifier']));
                $question_answers[$answer['identifier']] = $text;
            }
        }
        
        //create answers and complex answers
        foreach ($matches as $id => $match)
        {
            $answer_title = $question_answers[$id];
            //echo $answer_title.'<br/>';
            $match_index = $this->check_match($question, $question_answers[$match['match']]);
            $opt = new MatchingQuestionOption($this->import_images($answer_title), $match_index, $match['score']);
            $question->add_option($opt);
        }
    }

    function check_match($question, $match)
    {
        $matches = $question->get_matches();
        $found = false;
        foreach ($matches as $i => $qmatch)
        {
            if ($match == $qmatch)
            {
                $found = true;
                return $i;
            }
        }
        if (! $found)
        {
            $question->add_match($this->import_images($match));
            return count($question->get_matches()) - 1;
        }
    }
}
?>