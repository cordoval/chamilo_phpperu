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
    private $page_number;
    private $context_path;
    private $survey_page;
    private $page_order;
    private $next_context_path;
    private $finished = false;
    
    /**
     * @var Survey
     */
    private $survey;

    function __construct($name, $parent, $context_path, $survey, $action, $page_order, $page_nr)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $action);
        $this->context_path = $context_path;
        $this->parent = $parent;
        $this->survey = $survey;
        $this->page_order = $page_order;
//        dump('context_path');
//        dump($this->context_path);
        $this->page_number = $page_nr;
        
//        $this->page_number = $this->survey->get_page_nr($this->context_path);
        
        $this->survey_page = $this->survey->get_survey_page($this->context_path);
//        dump('survey_page');

//        dump($this->survey_page);
        $this->buildForm();
    }

    function buildForm()
    {
        $this->addElement('hidden', 'survey_page', $this->survey_page->get_id());
       	$this->addElement('hidden', 'context_path', $this->context_path);
        // Add buttons
        if ($this->page_number > 1)
        {
            $buttons[] = $this->createElement('style_submit_button', self :: BACK_BUTTON, Translation :: get('Back'), array('class' => 'previous'), $this->page_order[$this->page_number - 2]);
        }
        
        if ($this->page_number < $this->survey->count_pages())
        {
            $buttons[] = $this->createElement('style_submit_button', self :: NEXT_BUTTON, Translation :: get('Next'), array('class' => 'next'), $this->page_order[$this->page_number]);
        
        }
        else
        {
            $buttons[] = $this->createElement('style_submit_button', self :: FINISH_BUTTON, Translation :: get('Finish'), array('class' => 'positive'), $this->page_order[$this->page_number - 1]);
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
           
            $question_display = SurveyQuestionDisplay :: factory($this, $complex_question, $answer, $question_context_path, $this->survey, $answers);
			if($answer){
				$answers[$complex_question->get_id()] = $answer;
			}
            
            //            dump($question_display);
            $question_display->display();
        }
               
        // Add buttons second time
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    function process_answers()
    {
        
        $form_values = $this->exportValues();
        $values = array();
        
//        dump($form_values);
        
        foreach ($form_values as $key => $value)
        {
            
            if (in_array($key, array(SurveyViewerForm :: FINISH_BUTTON, SurveyViewerForm :: NEXT_BUTTON, SurveyViewerForm :: BACK_BUTTON)))
            {
                $this->next_context_path = $value;
                if ($key == SurveyViewerForm :: FINISH_BUTTON)
                {
                    $this->finished = true;
                    $this->parent->finished($this->parent->get_progress());
                }
            }
            
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $count = count($split_key);
            $complex_question_id = $split_key[0];
            
            if (is_numeric($complex_question_id))
            {
               if (!StringUtilities :: is_null_or_empty($value, true))
                {
                    $answer_index = $split_key[1];
                    if ($count == 3)
                    {
                        $sub_index = $split_key[2];
                        $values[$complex_question_id][$answer_index][$sub_index] = $value;
                    }
                    else
                    {
                        $values[$complex_question_id][$answer_index] = $value;
                    }
                }
            }
        }
        
//            dump($values);
//            exit;
        $complex_question_ids = array_keys($values);
        
        if (count($complex_question_ids) > 0)
        {
            foreach ($complex_question_ids as $complex_question_id)
            {
                $answers = $values[$complex_question_id];
                
                if (count($answers) > 0)
                {
//                    dump($answer);
                	$this->parent->save_answer($complex_question_id, $answers, $this->context_path . '_' . $complex_question_id);
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

    function get_page_number()
    {
        return $this->page_number;
    }

    function get_survey_page()
    {
        return $this->survey_page;
    }

    function get_context_path()
    {
        return $this->context_path;
    }

    function get_question_context_paths()
    {
        return $this->survey->get_page_question_context_paths($this->get_context_path());
    }

}
?>