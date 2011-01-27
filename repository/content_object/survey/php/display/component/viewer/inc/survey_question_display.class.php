<?php
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
use common\libraries\Utilities;
use repository\content_object\survey_page\SurveyPage;

abstract class SurveyQuestionDisplay
{
    private $complex_question;
    private $question;
    private $question_nr;
    
    /**
     * @var SurveyViewerWizardPage
     */
    private $formvalidator;
    private $renderer;
    
    /**
     * @var Survey
     */
    private $survey;
    private $page_nr;
    private $answer;
    private $visible;
    private $contex_path;
    private $page_answers;

    function __construct($formvalidator, $complex_question, $question, $answer, $context_path, $survey = null, $page_answers)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        $this->complex_question = $complex_question;
        $this->page_answers = $page_answers;
        $this->question = $question;
        $this->answer = $answer;
        $this->contex_path = $context_path;
        $this->survey = $survey;
        if ($survey)
        {
            $this->question_nr = $this->survey->get_question_nr($context_path);
        }
        else
        {
            $this->question_nr = 1;
        }
    
    }

    function get_complex_question()
    {
        return $this->complex_question;
    }

    function get_question()
    {
        return $this->question;
    }

    function get_renderer()
    {
        return $this->renderer;
    }

    function get_formvalidator()
    {
        return $this->formvalidator;
    }

    function get_answer()
    {
        return $this->answer;
    }

    function get_context_path()
    {
        return $this->contex_path;
    }

    function get_survey()
    {
        return $this->survey;
    }

    function get_page_answers()
    {
        return $this->page_answers;
    }

    function display()
    {
        $formvalidator = $this->formvalidator;
        $this->add_header();
        if ($this->add_borders())
        {
            $header = array();
            $header[] = $this->get_instruction();
            $header[] = '<div class="with_borders">';
            
            $formvalidator->addElement('html', implode("\n", $header));
        }
        $this->add_question_form();
        
        if ($this->add_borders())
        {
            $footer = array();
            $footer[] = '<div class="clear"></div>';
            $footer[] = '</div>';
            $formvalidator->addElement('html', implode("\n", $footer));
        }
        $this->add_footer();
    }

    abstract function add_question_form();

    function add_header()
    {
        $formvalidator = $this->formvalidator;
        
        if (! $this->get_complex_question()->is_visible())
        {
            if ($this->get_answer())
            {
                $html[] = '<div  class="question" id="survey_question_' . $this->complex_question->get_id() . '">';
                $html[] = '<a name=' . $this->complex_question->get_id() . '></a>';
            }
            else
            {
                $context_path = $this->get_context_path();
                if ($this->get_survey()->has_context())
                {
                    $path_ids = explode('|', $context_path);
                    $ids = explode('_', $path_ids[2]);
                    $page_id = $ids[0];
                }
                else
                {
                    $ids = explode('_', $context_path);
                    $page_id = $ids[1];
                }
                
                if ($this->is_question_visible($page_id))
                {
                    $html[] = '<div  class="question" id="survey_question_' . $this->complex_question->get_id() . '">';
                    $html[] = '<a name=' . $this->complex_question->get_id() . '></a>';
                }
                else
                {
                    $html[] = '<div style="display:none" class="question" id="survey_question_' . $this->complex_question->get_id() . '">';
                }
            }
        }
        else
        {
            $html[] = '<div  class="question" id="survey_question_' . $this->complex_question->get_id() . '">';
            $html[] = '<a name=' . $this->complex_question->get_id() . '></a>';
        }
        
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
        $title = $this->question->get_title();
        $html[] = $this->parse($title);
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';
        
        $description = $this->question->get_description();
        if ($this->question->has_description())
        {
            $html[] = '<div class="description">';
            
            $html[] = $this->parse($description);
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div class="clear"></div>';
        
        $header = implode("\n", $html);
        $formvalidator->addElement('html', $header);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        
        $html[] = '</div>';
        $html[] = '</div>';
        
        $footer = implode("\n", $html);
        $formvalidator->addElement('html', $footer);
    }

    function add_borders()
    {
        return false;
    }

    abstract function get_instruction();

    static function factory($formvalidator, $complex_question, $answer, $context_path, $survey, $page_answers)
    {
        
        $question = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_question->get_ref());
        
        $type = $question->get_type();
        
        $file = dirname(__FILE__) . '/survey_question_display/' . $type . '.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'Display';
        $question_display = new $class($formvalidator, $complex_question, $question, $answer, $context_path, $survey, $page_answers);
        return $question_display;
    }

    function parse($value)
    {
        
        if ($this->get_survey())
        {
//            dump($this->get_survey()->get_invitee_id());
//        	dump(Survey :: parse($this->get_survey()->get_invitee_id(), $this->get_context_path(), $value));
        	return Survey :: parse($this->get_survey()->get_invitee_id(), $this->get_context_path(), $value);
        }
        else
        {
            return value;
        }
    
    }

    function is_question_visible($page_id)
    {
        $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($page_id);
        $configs = $survey_page->get_config();
	       
        $page_answers = $this->get_page_answers();
              
        foreach ($configs as $config)
        {
            
            foreach ($page_answers as $question_id => $answer_to_match)
            {
                
                $from_question_id = $config[SurveyPage :: FROM_VISIBLE_QUESTION_ID];
                if ($question_id == $from_question_id)
                {
                   
                    $answer = $config[SurveyPage :: ANSWERMATCHES];
                    $answers_to_match = array();
                    foreach ($answer as $key => $value)
                    {
                        $oids = explode('_', $key);
                        if (count($oids) == 2)
                        {
                            $answers_to_match[] = $oids[1];
                        }
                        elseif (count($oids) == 3)
                        {
                            $option = $oids[1];
                            $answers_to_match[$option] = $value;
                        
                        }
                    }

                    $diff = array_diff($answers_to_match, $answer_to_match);
                    if (count($diff) == 0)
                    {
                        if(in_array($this->get_complex_question()->get_id(), $config[SurveyPage :: TO_VISIBLE_QUESTIONS_IDS]))
                        {
                        	return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}
?>