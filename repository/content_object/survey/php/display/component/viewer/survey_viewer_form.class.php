<?php
namespace repository\content_object\survey;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Security;
use common\libraries\StringUtilities;

class SurveyViewerForm extends FormValidator
{
    
    const FORM_NAME = 'survey_viewer_form';
    
    const BACK_BUTTON = 'back';
    const NEXT_BUTTON = 'next';
    const FINISH_BUTTON = 'finish';
    
    private $parent;
    private $context_path;
    private $next_context_path;
    private $back_context_path;
    private $survey_page;
    private $user_id;
    private $publication_id;
    private $page_answers;
//    private $finished = false;
    
    /**
     * @var Survey
     */
    private $survey;

    function __construct($parent, $context_path, $survey, $action, $next_context_path , $back_context_path , $user_id, $publication_id)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $action);
        $this->context_path = $context_path;
        
        $this->next_context_path = $next_context_path;
//        dump($this->next_context_path);
        
        $this->back_context_path = $back_context_path;
//         dump($this->back_context_path);
        $this->parent = $parent;
        $this->survey = $survey;
//        $this->page_order = $page_order;
        //        dump('context_path');
        //        dump($this->context_path);
//        $this->page_number = $page_nr;
        $this->user_id = $user_id;
        $this->publication_id = $publication_id;
        
        //        $this->page_number = $this->survey->get_page_nr($this->context_path);
        

        $this->survey_page = $this->survey->get_survey_page($this->context_path);
        //        dump('survey_page');
        $this->set_page_visible_question_answers();

        //        dump($this->survey_page);
        $this->buildForm();
    }

    function buildForm()
    {
        $this->addElement('hidden', 'survey_page', $this->survey_page->get_id());
        $this->addElement('hidden', 'context_path', $this->context_path);
        $this->addElement('hidden', 'user_id', $this->user_id);
        $this->addElement('hidden', 'publication_id', $this->publication_id);
        
        // Add buttons
        //        if ($this->page_number > 1)
        if ($this->back_context_path)
        {
            $buttons[] = $this->createElement('style_submit_button', self :: BACK_BUTTON, Translation :: get('Back'), array(
                    'class' => 'previous'), $this->back_context_path);
        }
        
        //        if ($this->page_number < $this->survey->count_pages())
        if ($this->next_context_path)
        
        {
            $buttons[] = $this->createElement('style_submit_button', self :: NEXT_BUTTON, Translation :: get('Next'), array(
                    'class' => 'next'), $this->next_context_path);
        
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', self :: FINISH_BUTTON, Translation :: get('Finish'), array(
                    'class' => 'positive'),$this->context_path);
        }
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        // Add question forms
        $complex_questions = $this->survey->get_page_complex_questions($this->context_path);
        //        dump('page_context_path '.$this->context_path);
        $answers = array();
        foreach ($complex_questions as $complex_question)
        {
        	$question_context_path = $this->context_path . '_' . $complex_question->get_id();
            
        	$answer = $this->parent->get_answer($complex_question->get_id(), $question_context_path);
            
            $question_display = SurveyQuestionDisplay :: factory($this, $complex_question, $answer, $question_context_path, $this->survey, $this->page_answers);
//            if ($answer)
//            {
//                $answers[$complex_question->get_id()] = $answer;
//            }
            
            //            dump($question_display);
            $question_display->display();
        }
        
        // Add buttons second time
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

	private function set_page_visible_question_answers()
    {
        //    	dump($context_path);
        $complex_questions = $this->survey_page->get_questions(true);
        while ($complex_question = $complex_questions->next_result())
        {
            //    		dump($complex_question->get_id());
            if ($complex_question->is_visible())
            {
                $complex_question_id = $complex_question->get_id();
                //    			dump('visible');
//                dump('qcp ' . $context_path . '_' . $complex_question_id);
                $answer = $this->parent->get_answer($complex_question_id, $this->context_path . '_' . $complex_question_id);
//                dump($answer);
                if ($answer)
                {
                    $this->page_answers[$complex_question_id] = $answer;
                
                }
            }
        }
    
    }

    function is_finished()
    {
        return $this->finished;
    }

    function get_next_context_path()
    {
        return $this->next_context_path;
    }

    function get_survey_page()
    {
        return $this->survey_page;
    }

    function get_context_path()
    {
        return $this->context_path;
    }

}
?>