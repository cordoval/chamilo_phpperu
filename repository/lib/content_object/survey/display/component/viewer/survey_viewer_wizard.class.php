<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_next.class.php';
require_once dirname(__FILE__) . '/wizard/survey_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_survey_viewer_wizard_page.class.php';

class SurveyViewerWizard extends HTML_QuickForm_Controller
{
    
    const PARAM_CURRENT_PAGE = 'current_page';
    
    private $parent;
    private $survey;
    private $template_id;
    private $tracker_count;
    private $tracker;
    private $context;
    
    private $total_pages;
    private $total_questions;
    private $pages;
    private $real_pages;
    private $question_visibility;

    function SurveyViewerWizard($parent)
    {
        //        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . $survey->get_id(), true);
        parent :: HTML_QuickForm_Controller('SurveyViewerWizard_' . '1', true);
        
        $this->parent = $parent;
        
        //        dump($this->get_parent()->get_parameters());
        

        //        dump($_POST);
        

        $survey_publication_id = $this->get_parent()->get_parameter(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        
        $this->publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($survey_publication_id);
        $this->survey = $this->publication->get_publication_object();
        
        $participant_id = $this->get_parent()->get_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT);
        
        if (! $participant_id)
        {
            
            //            dump('no participant_id');
            $user_id = $this->get_parent()->get_user_id();
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $survey_publication_id);
            $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
            
            $tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
            
            if ($tracker_count == 0)
            {
                if (! $this->publication->create_participant_trackers($user_id))
                {
                    $user = UserDataManager :: get_instance()->retrieve_user($user_id);
                    $message = 'NoContextAvailableForUser: ';
                    $this->get_parent()->redirect(Translation :: get($message) . $user->get_username(), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
                }
            }
            
            $this->tracker = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
            $participant_id = $this->tracker->get_id();
            $this->parent->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $participant_id);
        }
        else
        {
            //            dump('participant_id set');
            $condition = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_ID, $participant_id);
            $this->tracker = Tracker :: get_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
            $this->parent->set_parameter(SurveyManager :: PARAM_SURVEY_PARTICIPANT, $participant_id);
        }
        
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID, $survey_publication_id);
        $conditions[] = new EqualityCondition(SurveyParticipantTracker :: PROPERTY_USER_ID, $this->tracker->get_user_id());
        $condition = new AndCondition($conditions);
        $this->tracker_count = Tracker :: count_data(SurveyParticipantTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
        
        if ($this->tracker->get_context_id() != 0)
        {
            $this->context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($this->tracker->get_context_id());
        }
        
        $this->add_pages();
        
        $this->addAction('next', new SurveyViewerWizardNext($this));
        $this->addAction('process', new SurveyViewerWizardProcess($this));
        $this->addAction('display', new SurveyViewerWizardDisplay($this));
        
    //        dump($this->get_parent()->get_parameters());
    }

    function add_pages()
    {
        
        $check_allowed_pages = true;
        if ($this->survey->get_context_template_id() == 0)
        {
            $check_allowed_pages = false;
        }
        else
        {
            $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_SURVEY_ID, $this->survey->get_id());
            $conditions[] = new EqualityCondition(SurveyContextTemplateRelPage :: PROPERTY_TEMPLATE_ID, $this->tracker->get_context_template_id());
            $condition = new AndCondition($conditions);
            //            dump($condition);
            //            exit;
            $template_rel_pages = SurveyContextDataManager :: get_instance()->retrieve_template_rel_pages($condition);
            $allowed_pages = array();
            while ($template_rel_page = $template_rel_pages->next_result())
            {
                $allowed_pages[] = $template_rel_page->get_page_id();
            }
        }
        
        $complex_survey_page_items = $this->survey->get_pages(true);
        $page_nr = 0;
        $question_nr = 0;
        $this->question_visibility = array();
        
        while ($survey_page_item = $complex_survey_page_items->next_result())
        {
            if ($check_allowed_pages)
            {
                if (! in_array($survey_page_item->get_ref(), $allowed_pages))
                {
                    continue;
                }
            }
            
            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_item->get_ref());
            
            $page_nr ++;
            $this->real_pages[$page_nr] = $survey_page->get_id();
            //             dump($page_nr);
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
            $questions = array();
            $questions_items = $survey_page->get_questions(true);
            
            while ($question_item = $questions_items->next_result())
            {
                $question = RepositoryDataManager :: get_instance()->retrieve_content_object($question_item->get_ref());
                
                if ($question_item->get_visible() == 1)
                {
                    $this->question_visibility[$question->get_id()] = true;
                }
                else
                {
                    $this->question_visibility[$question->get_id()] = false;
                }
                
                if ($question->get_type() == SurveyDescription :: get_type_name())
                {
                    $questions[$question->get_id() . 'description'] = $question;
                }
                else
                {
                    if ($question_item->get_visible() == 1)
                    {
                        $question_nr ++;
                        $questions[$question_nr] = $question;
                    }
                    else
                    {
                        //                    	$question_nr ++;
                        $bis_nr = $question_nr . '.1';
                        $questions[$bis_nr] = $question;
                    }
                
                }
            
            }
            
            $this->pages[$page_nr] = array(page => $survey_page, questions => $questions);
            
        //            dump('pagenr:'.$page_nr);
        }
        
        if ($page_nr == 0)
        {
            
            $this->addPage(new QuestionsSurveyViewerWizardPage('question_page_' . $page_nr, $this, $page_nr));
        }
        
        //        dump(array_keys($this->pages));
        

        $this->total_pages = $page_nr;
        $this->total_questions = $question_nr;
    
    }

    function get_questions($page_number)
    {
        $page = $this->pages[$page_number];
        $questions = $page['questions'];
        return $questions;
    }

    function get_page($page_number)
    {
        $page = $this->pages[$page_number];
        $page_object = $page['page'];
        return $page_object;
    }

    function get_real_page_nr($page_nr)
    {
        return $this->real_pages[$page_nr];
    }

    function get_question_visibility($question_id)
    {
        return $this->question_visibility[$question_id];
    }

    function get_parent()
    {
        return $this->parent;
    }

    function get_survey()
    {
        return $this->survey;
    }

    function get_tracker_count()
    {
        return $this->tracker_count;
    }

    function get_participant_id()
    {
        return $this->tracker->get_id();
    }

    function get_total_pages()
    {
        return $this->total_pages;
    }

    function get_total_questions()
    {
        $count = 0;
        
        foreach ($this->question_visibility as $visible)
        {
            if ($visible)
            {
                $count = $count + 1;
            }
        }
        
        return $count;
    }

    function save_answer($complex_question_id, $answer)
    {
        
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $this->tracker->get_id());
        $conditions[] = new EqualityCondition(SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID, $complex_question_id);
        $condition = new AndCondition($conditions);
        $tracker_count = $trackers = tracker :: count_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition);
               
        if ($tracker_count == 1)
        {
            $tracker = tracker :: get_data(SurveyQuestionAnswerTracker :: get_table_name(), SurveyManager :: APPLICATION_NAME, $condition, 0, 1)->next_result();
            $tracker->set_answer($answer);
            $tracker->update();
        }
        else
        {
            $parameters = array();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] = $this->tracker->get_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] = $this->tracker->get_context_id();
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_QUESTION_CID] = $complex_question_id;
            $parameters[SurveyQuestionAnswerTracker :: PROPERTY_ANSWER] = $answer;
            
            Event :: trigger(SurveyQuestionAnswerTracker :: SAVE_QUESTION_ANSWER_EVENT, SurveyManager :: APPLICATION_NAME, $parameters);
        }
        //test for better tracing of setting status of trackers.
    }

    function parse($value)
    {
        
        if ($this->context)
        {
            $explode = explode('$V{', $value);
            
            $new_value = array();
            foreach ($explode as $part)
            {
                
                $vars = explode('}', $part);
                
                if (count($vars) == 1)
                {
                    $new_value[] = $vars[0];
                }
                else
                {
                    $var = $vars[0];
                    
                    $replace = $this->context->get_additional_property($var);
                    
                    $new_value[] = $replace . ' ' . $vars[1];
                }
            
            }
            return implode(' ', $new_value);
        }
        else
        {
            return $value;
        }
    
    }

}
?>